import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Employees({ rows, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.employees')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.employees')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="employees"
                routeName="reports.employees"
                filters={filters}
                exportType="employees"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('report_filters.employee_name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.father_name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.position')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.phone')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.salary')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.joining_date')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.name}</td>
                                <td className="px-4 py-3">{row.father_name || '—'}</td>
                                <td className="px-4 py-3">{row.position || '—'}</td>
                                <td className="px-4 py-3">{row.phone || '—'}</td>
                                <td className="px-4 py-3">{t(`status.${row.status}`)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.salary)}</td>
                                <td className="px-4 py-3">{row.joining_date_shamsi || row.joining_date}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
