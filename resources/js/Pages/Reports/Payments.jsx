import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import CustomerSelect from '@/Components/Erp/CustomerSelect';
import ReportExportButtons from '@/Components/Erp/ReportExportButtons';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import TextInput from '@/Components/TextInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { buildExportQuery } from '@/utils/reportExport';
import { resourceData } from '@/utils/resource';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Payments({ rows, filters, customers, selectedCustomer }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const [customerId, setCustomerId] = useState(filters?.customer_id || '');
    const [receiptNumber, setReceiptNumber] = useState(filters?.receipt_number || '');
    const list = Array.isArray(rows) ? rows : [];
    const exportQuery = buildExportQuery({
        date_from: dateFrom,
        date_to: dateTo,
        customer_id: customerId,
        receipt_number: receiptNumber,
    });

    return (
        <ErpLayout>
            <Head title={t('reports.payments')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.payments')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <form onSubmit={(e) => {
                e.preventDefault();
                router.get(route('reports.payments'), {
                    date_from: dateFrom,
                    date_to: dateTo,
                    customer_id: customerId,
                    receipt_number: receiptNumber,
                }, { preserveState: true });
            }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <div className="min-w-[220px]">
                    <label className="mb-1 block text-sm font-medium text-gray-700">{t('fields.customer')}</label>
                    <CustomerSelect
                        customers={customers}
                        value={customerId}
                        onChange={setCustomerId}
                        selectedCustomer={resourceData(selectedCustomer)}
                    />
                </div>
                <div>
                    <label className="mb-1 block text-sm font-medium text-gray-700">{t('fields.receipt_number')}</label>
                    <TextInput
                        className="mt-0 block w-full min-w-[180px]"
                        value={receiptNumber}
                        onChange={(e) => setReceiptNumber(e.target.value)}
                        placeholder={t('actions.search')}
                    />
                </div>
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
