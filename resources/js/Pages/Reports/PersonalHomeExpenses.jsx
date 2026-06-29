import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import { formatMoney } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function PersonalHomeExpenses({ rows, filters, total }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.personal_home_expenses')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.personal_home_expenses')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="personal-home-expenses"
                routeName="reports.personal-home-expenses"
                filters={filters}
                exportType="personal-home-expenses"
            />

            <div className="mb-4">
                <StatCard title={t('fields.total_amount')} value={formatMoney(total)} color="amber" />
            </div>

            <p className="mb-4 text-sm text-gray-600">{t('reports.total_records')}: {list.length}</p>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.expense_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{row.item_name}</td>
                                <td className="px-4 py-3">{formatMoney(row.amount)}</td>
                                <td className="px-4 py-3">{row.description || '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
