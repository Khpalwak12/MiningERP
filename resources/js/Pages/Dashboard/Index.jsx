import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency, formatMoney, formatNumber } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function Index({ stats, recentShipments, recentPayments }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('dashboard.title')} />
            <FlashMessage />

            <h1 className="mb-6 text-2xl font-bold text-gray-900">{t('dashboard.title')}</h1>

            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title={t('dashboard.marble_sales')}
                    value={formatCurrency(stats?.marble_sales?.sales)}
                    subtitle={`${stats?.marble_sales?.trucks || 0} ${t('dashboard.marble_trucks')} / ${formatNumber(stats?.marble_sales?.tons, 1)} ${t('dashboard.marble_tons')}`}
                    color="indigo"
                />
                <StatCard
                    title={t('dashboard.total_income')}
                    value={formatCurrency(stats?.total_revenue)}
                    color="green"
                />
                <StatCard
                    title={t('dashboard.total_expenses')}
                    value={formatCurrency(stats?.total_expenses)}
                    color="red"
                />
                <StatCard
                    title={t('reports.outstanding_balance')}
                    value={formatCurrency(stats?.outstanding_balances)}
                    color="amber"
                />
            </div>

            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard title={t('dashboard.active_employees')} value={stats?.employee_count} color="blue" />
                <StatCard title={t('dashboard.payments_received')} value={formatCurrency(stats?.cash_flow?.income)} color="green" />
                <StatCard title={t('dashboard.net_profit')} value={formatCurrency(stats?.net_profit)} color="indigo" />
            </div>

            {can('contractor-royalty.view') && (
                <div className="mb-6 grid gap-4 sm:grid-cols-2">
                    <StatCard
                        title={t('dashboard.contractor_total_royalties')}
                        value={formatCurrency(stats?.contractor_royalty?.total_royalties)}
                        subtitle={`${stats?.contractor_royalty?.dispatch_count || 0} ${t('dashboard.contractor_dispatches')} / ${formatNumber(stats?.contractor_royalty?.total_tons, 2)} ${t('dashboard.contractor_tons')}`}
                        color="indigo"
                    />
                    <StatCard title={t('dashboard.contractor_outstanding_balance')} value={formatCurrency(stats?.contractor_royalty?.outstanding_balance)} color="amber" />
                </div>
            )}

            {can('machinery.view') && (
                <div className="mb-6 grid gap-4 sm:grid-cols-2">
                    <StatCard title={t('dashboard.machinery_total_afn')} value={formatMoney(stats?.machinery?.AFN)} color="amber" />
                    <StatCard title={t('dashboard.machinery_total_usd')} value={formatMoney(stats?.machinery?.USD, 'USD')} color="indigo" />
                </div>
            )}

            <div className="grid gap-6 lg:grid-cols-2">
                <div className="rounded-lg bg-white p-4 shadow">
                    <h2 className="mb-3 font-semibold">{t('dashboard.recent_shipments')}</h2>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-gray-500">
                                    <th className="py-2">{t('fields.date')}</th>
                                    <th className="py-2">{t('fields.customer')}</th>
                                    <th className="py-2">{t('fields.total_amount')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentShipments?.data?.map((s) => (
                                    <tr key={s.id} className="border-b">
                                        <td className="py-2">{s.shipment_date_shamsi}</td>
                                        <td className="py-2">{s.customer?.name}</td>
                                        <td className="py-2">{formatCurrency(s.total_amount)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="rounded-lg bg-white p-4 shadow">
                    <h2 className="mb-3 font-semibold">{t('dashboard.recent_payments')}</h2>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-gray-500">
                                    <th className="py-2">{t('fields.date')}</th>
                                    <th className="py-2">{t('fields.customer')}</th>
                                    <th className="py-2">{t('fields.amount')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentPayments?.data?.map((p) => (
                                    <tr key={p.id} className="border-b">
                                        <td className="py-2">{p.payment_date_shamsi}</td>
                                        <td className="py-2">{p.customer?.name}</td>
                                        <td className="py-2">{formatCurrency(p.amount)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </ErpLayout>
    );
}
