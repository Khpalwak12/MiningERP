import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ReportExportButtons from '@/Components/Erp/ReportExportButtons';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { buildExportQuery } from '@/utils/reportExport';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Payments({ rows, filters }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const list = Array.isArray(rows) ? rows : [];
    const exportQuery = buildExportQuery({ date_from: dateFrom, date_to: dateTo });

    return (
        <ErpLayout>
            <Head title={t('reports.payments')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.payments')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <form onSubmit={(e) => { e.preventDefault(); router.get(route('reports.payments'), { date_from: dateFrom, date_to: dateTo }, { preserveState: true }); }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
                <ReportExportButtons exportType="payments" queryString={exportQuery} />
            </form>
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.receipt_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.received_by')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.map((r) => (
                            <tr key={r.id}>
                                <td className="px-4 py-3">{r.payment_date_shamsi || r.payment_date}</td>
                                <td className="px-4 py-3">{r.customer?.name}</td>
                                <td className="px-4 py-3">{r.receipt_number || '—'}</td>
                                <td className="px-4 py-3">{r.received_by}</td>
                                <td className="px-4 py-3">{formatCurrency(r.amount)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
