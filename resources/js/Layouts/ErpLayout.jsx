import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import LocaleSwitcher from '@/Components/Erp/LocaleSwitcher';
import FinancialYearModeBanner from '@/Components/Erp/FinancialYearModeBanner';
import useTranslation from '@/hooks/useTranslation';
import useSyncDocumentDirection from '@/hooks/useSyncDocumentDirection';
import usePermission from '@/hooks/usePermission';

const navItems = [
    { route: 'dashboard', permission: 'dashboard.view', key: 'nav.dashboard' },
    { route: 'customers.index', permission: 'customers.view', key: 'nav.customers' },
    { route: 'shipments.index', permission: 'shipments.view', key: 'nav.shipments' },
    { route: 'payments.index', permission: 'payments.view', key: 'nav.payments' },
    { route: 'sankari.index', permission: 'sankari.view', key: 'nav.sankari' },
    { route: 'expenses.index', permission: 'expenses.view', key: 'nav.expenses' },
    { route: 'employees.index', permission: 'employees.view', key: 'nav.employees' },
    { route: 'payroll.index', permission: 'payroll.view', key: 'nav.payroll' },
    { route: 'inventory.index', permission: 'inventory.view', key: 'nav.inventory' },
    { route: 'mine-assets.index', permission: 'mine-assets.view', key: 'nav.mine_assets' },
    { route: 'personal-accounts.contacts.index', permission: 'personal-accounts.view', key: 'nav.personal_accounts' },
    { route: 'contractor-royalty.ledger', permission: 'contractor-royalty.view', key: 'nav.contractor_royalty' },
    { route: 'journal-entries.index', permission: 'accounting.view', key: 'nav.accounting' },
    { route: 'financial-years.index', permission: 'financial-year.view', key: 'nav.financial_years' },
    { route: 'reports.index', permission: 'reports.view', key: 'nav.reports' },
    { route: 'users.index', permission: 'users.view', key: 'nav.users' },
    { route: 'roles.index', permission: 'roles.view', key: 'nav.roles' },
];

export default function ErpLayout({ children }) {
    const user = usePage().props.auth.user;
    const { t, isRtl } = useTranslation();
    useSyncDocumentDirection();
    const { can } = usePermission();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const visibleNav = navItems.filter((item) => can(item.permission));

    const sidebarPosition = isRtl ? 'right-0' : 'left-0';
    const sidebarHidden = isRtl ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0';
    const sidebarVisible = 'translate-x-0';

    const isActive = (routeName) => {
        try {
            if (route().current(routeName)) {
                return true;
            }
            if (routeName.endsWith('.index')) {
                return route().current(routeName.replace('.index', '.*'));
            }
            return false;
        } catch {
            return false;
        }
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <div className="lg:flex lg:flex-row">
                <aside
                    className={`fixed inset-y-0 ${sidebarPosition} z-40 w-64 transform bg-slate-900 text-white transition-transform lg:static lg:translate-x-0 ${
                        sidebarOpen ? sidebarVisible : sidebarHidden
                    }`}
                >
                    <div className="flex h-16 items-center gap-2 border-b border-slate-700 px-4">
                        <ApplicationLogo className="h-8 w-auto fill-white" />
                        <span className="text-sm font-semibold">{t('app_name')}</span>
                    </div>
                    <nav className="space-y-1 p-3">
                        {visibleNav.map((item) => (
                            <Link
                                key={item.route}
                                href={route(item.route)}
                                className={`block rounded-md px-3 py-2 text-sm transition ${
                                    isActive(item.route)
                                        ? 'bg-indigo-600 text-white'
                                        : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                                }`}
                            >
                                {t(item.key)}
                            </Link>
                        ))}
                    </nav>
                </aside>

                <div className="flex min-h-screen flex-1 flex-col">
                    <header className="flex h-16 items-center justify-between border-b bg-white px-4 shadow-sm">
                        <button
                            type="button"
                            className="rounded p-2 text-gray-600 lg:hidden"
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                        >
                            <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div className="ml-auto flex items-center gap-3">
                            <LocaleSwitcher />
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <button type="button" className="text-sm font-medium text-gray-700">
                                        {user.name}
                                    </button>
                                </Dropdown.Trigger>
                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>{t('nav.profile')}</Dropdown.Link>
                                    <Dropdown.Link href={route('logout')} method="post" as="button">
                                        {t('nav.logout')}
                                    </Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>
                    </header>

                    <main className="flex-1 p-4 sm:p-6">
                        <FinancialYearModeBanner />
                        {children}
                    </main>
                </div>
            </div>

            {sidebarOpen && (
                <div className="fixed inset-0 z-30 bg-black/40 lg:hidden" onClick={() => setSidebarOpen(false)} />
            )}
        </div>
    );
}
