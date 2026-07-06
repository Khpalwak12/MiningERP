const { app, BrowserWindow, Menu, dialog, shell, ipcMain } = require('electron');
const { spawn, spawnSync } = require('child_process');
const fs = require('fs');
const http = require('http');
const net = require('net');
const path = require('path');

const isDev = !app.isPackaged;
const PRODUCT_NAME = 'Mining ERP';
const DEFAULT_PORT = 19647;

let phpProcess = null;
let mainWindow = null;
let splashWindow = null;
let serverPort = DEFAULT_PORT;
let isQuitting = false;

const gotTheLock = app.requestSingleInstanceLock();

if (!gotTheLock) {
    app.quit();
}

function resolvePaths() {
    if (isDev) {
        const laravelRoot = path.resolve(__dirname, '..');
        const phpRoot = path.join(__dirname, 'build-resources', 'php');

        return {
            laravelRoot,
            phpBinary: path.join(phpRoot, 'php.exe'),
            phpIni: path.join(phpRoot, 'php.ini'),
            iconPath: path.join(__dirname, 'build-resources', 'icon.png'),
        };
    }

    const resources = process.resourcesPath;

    return {
        laravelRoot: path.join(resources, 'laravel'),
        phpBinary: path.join(resources, 'php', 'php.exe'),
        phpIni: path.join(resources, 'php', 'php.ini'),
        iconPath: path.join(resources, 'icon.png'),
    };
}

function getDataPath() {
    return path.join(app.getPath('appData'), 'MiningERP');
}

function ensureDataDirectories(dataPath) {
    const directories = [
        dataPath,
        path.join(dataPath, 'backups'),
        path.join(dataPath, 'exports'),
        path.join(dataPath, 'logs'),
        path.join(dataPath, 'uploads'),
        path.join(dataPath, 'tmp'),
        path.join(dataPath, 'bootstrap', 'cache'),
        path.join(dataPath, 'storage', 'app', 'public'),
        path.join(dataPath, 'storage', 'app', 'private'),
        path.join(dataPath, 'storage', 'app', 'mpdf-tmp'),
        path.join(dataPath, 'storage', 'fonts'),
        path.join(dataPath, 'storage', 'framework', 'cache'),
        path.join(dataPath, 'storage', 'framework', 'sessions'),
        path.join(dataPath, 'storage', 'framework', 'views'),
        path.join(dataPath, 'storage', 'logs'),
    ];

    for (const directory of directories) {
        fs.mkdirSync(directory, { recursive: true });
    }
}

function buildPhpEnvironment(dataPath, port) {
    const tempDirectory = path.join(dataPath, 'tmp');
    const normalizePath = (value) => value.replace(/\\/g, '/');

    return {
        ...process.env,
        MININGERP_DESKTOP: '1',
        MININGERP_DATA_PATH: dataPath,
        LARAVEL_STORAGE_PATH: normalizePath(path.join(dataPath, 'storage')),
        DB_CONNECTION: 'sqlite',
        DB_DATABASE: path.join(dataPath, 'database.sqlite'),
        APP_ENV: 'production',
        APP_DEBUG: 'false',
        APP_URL: `http://127.0.0.1:${port}`,
        ERP_BACKUP_PATH: path.join(dataPath, 'backups'),
        LOG_CHANNEL: 'single',
        BROWSER: 'none',
        NO_COLOR: '1',
        TMP: normalizePath(tempDirectory),
        TEMP: normalizePath(tempDirectory),
    };
}

function runPhpCommand(paths, args, dataPath, port) {
    const env = buildPhpEnvironment(dataPath, port);
    const phpArgs = [];

    if (fs.existsSync(paths.phpIni)) {
        phpArgs.push('-c', paths.phpIni);
    }

    phpArgs.push(...args);

    const result = spawnSync(paths.phpBinary, phpArgs, {
        cwd: paths.laravelRoot,
        env,
        windowsHide: true,
        encoding: 'utf8',
    });

    if (result.status !== 0) {
        const output = `${result.stdout || ''}\n${result.stderr || ''}`.trim();
        throw new Error(output || 'PHP command failed.');
    }

    return result.stdout || '';
}

function getFreePort() {
    return new Promise((resolve, reject) => {
        const server = net.createServer();

        server.listen(0, '127.0.0.1', () => {
            const address = server.address();
            const port = typeof address === 'object' && address ? address.port : DEFAULT_PORT;
            server.close(() => resolve(port));
        });

        server.on('error', reject);
    });
}

function waitForServer(url, attempts = 120) {
    return new Promise((resolve, reject) => {
        let tries = 0;

        const check = () => {
            tries += 1;

            const request = http.get(url, (response) => {
                response.resume();
                resolve();
            });

            request.on('error', () => {
                if (tries >= attempts) {
                    reject(new Error('Mining ERP backend did not start in time.'));
                    return;
                }

                setTimeout(check, 500);
            });
        };

        check();
    });
}

function startPhpServer(paths, dataPath, port) {
    const env = buildPhpEnvironment(dataPath, port);
    const phpArgs = [];

    if (fs.existsSync(paths.phpIni)) {
        phpArgs.push('-c', paths.phpIni);
    }

    phpArgs.push('artisan', 'serve', `--port=${port}`, '--host=127.0.0.1');

    phpProcess = spawn(paths.phpBinary, phpArgs, {
        cwd: paths.laravelRoot,
        env,
        windowsHide: true,
        stdio: ['ignore', 'pipe', 'pipe'],
    });

    phpProcess.stdout?.on('data', (data) => {
        fs.appendFileSync(path.join(dataPath, 'logs', 'php-server.log'), data.toString());
    });

    phpProcess.stderr?.on('data', (data) => {
        fs.appendFileSync(path.join(dataPath, 'logs', 'php-server.log'), data.toString());
    });
}

function shutdownPhp() {
    if (!phpProcess || phpProcess.killed) {
        return;
    }

    if (process.platform === 'win32') {
        spawn('taskkill', ['/pid', String(phpProcess.pid), '/f', '/t'], {
            windowsHide: true,
            stdio: 'ignore',
        });
    } else {
        phpProcess.kill('SIGTERM');
    }

    phpProcess = null;
}

function createSplashWindow(paths) {
    splashWindow = new BrowserWindow({
        width: 460,
        height: 300,
        frame: false,
        resizable: false,
        center: true,
        show: false,
        icon: paths.iconPath,
        webPreferences: {
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
        },
    });

    splashWindow.loadFile(path.join(__dirname, 'splash.html'));
    splashWindow.once('ready-to-show', () => splashWindow?.show());
}

function createMainWindow(paths, appUrl) {
    mainWindow = new BrowserWindow({
        width: 1440,
        height: 900,
        minWidth: 1100,
        minHeight: 700,
        show: false,
        title: PRODUCT_NAME,
        icon: paths.iconPath,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
            devTools: isDev,
        },
    });

    mainWindow.webContents.on('context-menu', (event) => {
        if (!isDev) {
            event.preventDefault();
        }
    });

    if (!isDev) {
        mainWindow.webContents.on('devtools-opened', () => {
            mainWindow.webContents.closeDevTools();
        });
    }

    mainWindow.once('ready-to-show', () => {
        if (splashWindow && !splashWindow.isDestroyed()) {
            splashWindow.close();
            splashWindow = null;
        }

        mainWindow.show();
        mainWindow.focus();
    });

    mainWindow.loadURL(appUrl);

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

function buildApplicationMenu() {
    const template = [
        {
            label: 'File',
            submenu: [
                {
                    label: 'Open Data Folder',
                    click: () => shell.openPath(getDataPath()),
                },
                { type: 'separator' },
                {
                    label: 'Exit',
                    accelerator: 'Alt+F4',
                    click: () => {
                        isQuitting = true;
                        app.quit();
                    },
                },
            ],
        },
        {
            label: 'View',
            submenu: [
                { role: 'resetZoom' },
                { role: 'zoomIn' },
                { role: 'zoomOut' },
                { type: 'separator' },
                { role: 'togglefullscreen' },
            ],
        },
        {
            label: 'Help',
            submenu: [
                {
                    label: 'About Mining ERP',
                    click: () => {
                        dialog.showMessageBox({
                            type: 'info',
                            title: 'About Mining ERP',
                            message: PRODUCT_NAME,
                            detail: `Version ${app.getVersion()}\nData: ${getDataPath()}`,
                        });
                    },
                },
            ],
        },
    ];

    Menu.setApplicationMenu(Menu.buildFromTemplate(template));
}

function validateRuntime(paths) {
    if (!fs.existsSync(paths.phpBinary)) {
        throw new Error(
            'Bundled PHP runtime is missing. Rebuild the installer using scripts/build-desktop.ps1 on a development machine.'
        );
    }

    if (!fs.existsSync(paths.laravelRoot)) {
        throw new Error('Bundled Laravel application files are missing.');
    }

    if (!fs.existsSync(path.join(paths.laravelRoot, 'vendor', 'autoload.php'))) {
        throw new Error('Bundled Composer dependencies are missing.');
    }

    if (!fs.existsSync(path.join(paths.laravelRoot, 'public', 'build', 'manifest.json'))) {
        throw new Error('Bundled frontend build is missing.');
    }
}

async function bootstrapApplication() {
    const paths = resolvePaths();
    validateRuntime(paths);

    const dataPath = getDataPath();
    ensureDataDirectories(dataPath);

    serverPort = await getFreePort();
    const appUrl = `http://127.0.0.1:${serverPort}`;

    createSplashWindow(paths);
    buildApplicationMenu();

    runPhpCommand(paths, ['artisan', 'desktop:initialize'], dataPath, serverPort);
    startPhpServer(paths, dataPath, serverPort);
    await waitForServer(`${appUrl}/login`);

    createMainWindow(paths, appUrl);
}

function showFatalError(error) {
    const message = error instanceof Error ? error.message : String(error);

    if (splashWindow && !splashWindow.isDestroyed()) {
        splashWindow.close();
        splashWindow = null;
    }

    dialog.showErrorBox('Mining ERP', message);
}

app.setName('MiningERP');
app.setAppUserModelId('com.miningerp.desktop');

ipcMain.handle('desktop:get-version', () => app.getVersion());
ipcMain.handle('desktop:get-data-path', () => getDataPath());
ipcMain.handle('desktop:show-notification', (_event, payload) => {
    const { Notification } = require('electron');

    if (!Notification.isSupported()) {
        return false;
    }

    new Notification({
        title: payload?.title || PRODUCT_NAME,
        body: payload?.body || '',
    }).show();

    return true;
});

app.whenReady().then(async () => {
    try {
        await bootstrapApplication();
    } catch (error) {
        showFatalError(error);
        app.quit();
    }
});

app.on('second-instance', () => {
    if (mainWindow) {
        if (mainWindow.isMinimized()) {
            mainWindow.restore();
        }

        mainWindow.focus();
    }
});

app.on('before-quit', () => {
    isQuitting = true;
    shutdownPhp();
});

app.on('window-all-closed', () => {
    shutdownPhp();

    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('will-quit', () => {
    shutdownPhp();
});

process.on('uncaughtException', (error) => {
    if (!isQuitting) {
        showFatalError(error);
    }
});
