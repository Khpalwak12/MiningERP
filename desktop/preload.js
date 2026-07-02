const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('miningDesktop', {
    getVersion: () => ipcRenderer.invoke('desktop:get-version'),
    getDataPath: () => ipcRenderer.invoke('desktop:get-data-path'),
    notify: (payload) => ipcRenderer.invoke('desktop:show-notification', payload),
});
