import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function ProfitLoss({ report, filters, matchesFilter = true }) {
    const { t } = useTranslation();

    const items = [
        { key: 'marble_sales', label: t('reports.marble_sales') },
        { key: 'sankari_sales', label: t('reports.sankari_sales') },
        { key: 'total_income', label: t('reports.total_income') },
        { key: 'operating_expenses', label: t('reports.operating_expenses') },
        { key: 'payroll_expenses', label: t('reports.payroll_expenses') },
        { key: 'total_expenses', label: t('reports.total_expenses') },
        { key: 'net_profit', label: t('reports.net_profit') },
    ];

    return (
        <ErpLayout>
            <Head title={t('reports.profit_loss')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.profit_loss')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="profit-loss"
                routeName="reports.profit-loss"
                filters={filters}
                exportType="profit-loss"
            />

            <div className="rounded-lg bg-white p-6 shadow">
                {!matchesFilter ? (
                    <p className="text-center text-gray-500">{t('messages.no_records')}</p>
                ) : (
                    <table className="min-w-full text-sm">
                        <tbody className="divide-y">
                            {items.map((item) => (
                                <tr key={item.key}>
                                    <td className="py-3 font-medium">{item.label}</td>
                                    <td className="py-3 text-right">{formatCurrency(report?.[item.key] ?? 0)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </ErpLayout>
    );
}
