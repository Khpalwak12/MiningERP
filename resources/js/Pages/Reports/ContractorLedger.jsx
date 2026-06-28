import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function ContractorLedger({ summary, transactions, filters }) {
    const { t } = useTranslation();
    const list = Array.isArray(transactions) ? transactions : [];

    const ledgerTypeLabel = (type) => {
        if (type === 'royalty') return t('contractor_royalty.royalty_entry');
        if (type === 'salary_charge') return t('contractor_royalty.salary_charge_entry');
        return t('contractor_royalty.payment_entry');
    };

    return (
        <ErpLayout>
            <Head title={t('reports.contractor_ledger')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.contractor_ledger')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <ReportFilterForm reportType="contractor-ledger" routeName="reports.contractor-ledger" filters={filters} exportType="contractor-ledger" />

            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard title={t('contractor_royalty.total_royalties')} value={formatCurrency(summary?.total_royalties)} color="indigo" />
                <StatCard title={t('contractor_royalty.total_salary_charges')} value={formatCurrency(summary?.total_salary_charges)} color="blue" />
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
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.date_shamsi}</td>
                                <td className="px-4 py-3">{ledgerTypeLabel(row.type)}</td>
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
