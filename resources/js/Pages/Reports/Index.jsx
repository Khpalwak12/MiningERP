import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link } from '@inertiajs/react';

const reportLinks = [
    { route: 'reports.sales', key: 'reports.sales' },
    { route: 'reports.payments', key: 'reports.payments' },
    { route: 'reports.expenses', key: 'reports.expenses' },
    { route: 'reports.employees', key: 'reports.employees' },
    { route: 'reports.payroll', key: 'reports.payroll' },
    { route: 'reports.inventory', key: 'reports.inventory' },
    { route: 'reports.daily-production', key: 'reports.daily_production' },
    { route: 'reports.monthly-production', key: 'reports.monthly_production' },
    { route: 'reports.customer-balances', key: 'reports.customer_balances' },
    { route: 'reports.profit-loss', key: 'reports.profit_loss' },
];

export default function Index() {
    const { t } = useTranslation();

    return (
        <ErpLayout>
            <Head title={t('reports.title')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('reports.title')}</h1>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {reportLinks.map((item) => (
                    <Link key={item.route} href={route(item.route)} className="rounded-lg bg-white p-6 shadow transition hover:shadow-md">
                        <h2 className="font-semibold text-indigo-700">{t(item.key)}</h2>
                    </Link>
                ))}
            </div>
        </ErpLayout>
    );
}
