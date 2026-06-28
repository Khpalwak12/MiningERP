import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency, formatNumber } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ summary, transactions, filters }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const list = Array.isArray(transactions) ? transactions : [];

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.ledger')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('contractor_royalty.ledger')}</h1>
            <ContractorRoyaltyNav active="contractor-royalty.ledger" />

            <form onSubmit={(e) => {
                e.preventDefault();
                router.get(route('contractor-royalty.ledger'), { date_from: dateFrom, date_to: dateTo }, { preserveState: true });
            }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
            </form>

            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard
                    title={t('contractor_royalty.total_royalties')}
                    value={formatCurrency(summary?.total_royalties)}
                    subtitle={`${summary?.dispatch_count || 0} ${t('dashboard.contractor_dispatches')} / ${formatNumber(summary?.total_tons, 2)} ${t('dashboard.contractor_tons')}`}
                    color="indigo"
                />
                <StatCard title={t('contractor_royalty.total_payments_received')} value={formatCurrency(summary?.total_payments)} color="green" />
                <StatCard title={t('contractor_royalty.outstanding_balance')} value={formatCurrency(summary?.outstanding_balance)} color="amber" />
            </div>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-left">{t('contractor_royalty.total_royalty')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={5} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.date_shamsi}</td>
                                <td className="px-4 py-3">{row.type === 'royalty' ? t('contractor_royalty.royalty_entry') : t('contractor_royalty.payment_entry')}</td>
                                <td className="px-4 py-3">{row.description}</td>
                                <td className="px-4 py-3">{row.royalty_amount != null ? formatCurrency(row.royalty_amount) : '—'}</td>
                                <td className="px-4 py-3">{row.payment_amount != null ? formatCurrency(row.payment_amount) : '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
