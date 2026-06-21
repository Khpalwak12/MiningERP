import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function TrialBalance({ rows, filters }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const list = Array.isArray(rows) ? rows : [];

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route('accounting.trial-balance'), { date_from: dateFrom, date_to: dateTo }, { preserveState: true });
    };

    const totalDebit = list.reduce((s, r) => s + (parseFloat(r.total_debit) || 0), 0);
    const totalCredit = list.reduce((s, r) => s + (parseFloat(r.total_credit) || 0), 0);

    return (
        <ErpLayout>
            <Head title={t('accounting.trial_balance')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('accounting.trial_balance')}</h1>
                <Link href={route('journal-entries.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <form onSubmit={handleFilter} className="mb-4 flex flex-wrap gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <div className="flex items-end"><PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton></div>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.account')}</th>
                            <th className="px-4 py-3 text-right">{t('fields.debit')}</th>
                            <th className="px-4 py-3 text-right">{t('fields.credit')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.map((row) => (
                            <tr key={row.account_id}>
                                <td className="px-4 py-3">{row.account?.code} - {row.account?.name}</td>
                                <td className="px-4 py-3 text-right">{formatCurrency(row.total_debit)}</td>
                                <td className="px-4 py-3 text-right">{formatCurrency(row.total_credit)}</td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot className="bg-gray-50 font-bold">
                        <tr>
                            <td className="px-4 py-3">{t('fields.total_amount')}</td>
                            <td className="px-4 py-3 text-right">{formatCurrency(totalDebit)}</td>
                            <td className="px-4 py-3 text-right">{formatCurrency(totalCredit)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </ErpLayout>
    );
}
