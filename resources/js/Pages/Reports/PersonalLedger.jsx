import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import { formatMoney } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function PersonalLedger({ summary, transactions, contactBalances, filters, contacts }) {
    const { t } = useTranslation();
    const list = Array.isArray(transactions) ? transactions : [];
    const balances = Array.isArray(contactBalances) ? contactBalances : [];

    const typeLabel = (type) => (type === 'credit'
        ? t('personal_transaction_types.credit')
        : t('personal_transaction_types.payment'));

    return (
        <ErpLayout>
            <Head title={t('reports.personal_ledger')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.personal_ledger')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="personal-ledger"
                routeName="reports.personal-ledger"
                filters={filters}
                exportType="personal-ledger"
                lookups={{ contacts }}
                showPersonalContact
                showPersonalCurrency
            />

            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <StatCard title={t('personal_accounts.total_credit_afn')} value={formatMoney(summary?.total_credit_afn)} color="indigo" />
                <StatCard title={t('personal_accounts.total_payment_afn')} value={formatMoney(summary?.total_payment_afn)} color="green" />
                <StatCard title={t('personal_accounts.balance_afn')} value={formatMoney(summary?.outstanding_afn)} color="amber" />
                <StatCard title={t('personal_accounts.total_credit_usd')} value={formatMoney(summary?.total_credit_usd, 'USD')} color="indigo" />
                <StatCard title={t('personal_accounts.total_payment_usd')} value={formatMoney(summary?.total_payment_usd, 'USD')} color="green" />
                <StatCard title={t('personal_accounts.balance_usd')} value={formatMoney(summary?.outstanding_usd, 'USD')} color="amber" />
            </div>

            <h2 className="mb-3 text-lg font-semibold">{t('personal_accounts.contact_balances')}</h2>
            <div className="mb-6 overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.type')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.balance_afn')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.balance_usd')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {balances.length === 0 && (
                            <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {balances.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3 font-medium">{row.name}</td>
                                <td className="px-4 py-3">{t(`personal_contact_types.${row.contact_type}`)}</td>
                                <td className="px-4 py-3">{formatMoney(row.balance_afn)}</td>
                                <td className="px-4 py-3">{formatMoney(row.balance_usd, 'USD')}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <h2 className="mb-3 text-lg font-semibold">{t('personal_accounts.ledger')}</h2>
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.currency')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_transaction_types.credit')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_transaction_types.payment')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.running_balance')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={8} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.date_shamsi}</td>
                                <td className="px-4 py-3">{row.contact_name}</td>
                                <td className="px-4 py-3">{typeLabel(row.type)}</td>
                                <td className="px-4 py-3">{t(`currencies.${row.currency}`)}</td>
                                <td className="px-4 py-3">{row.credit_amount != null ? formatMoney(row.credit_amount, row.currency) : '—'}</td>
                                <td className="px-4 py-3">{row.payment_amount != null ? formatMoney(row.payment_amount, row.currency) : '—'}</td>
                                <td className="px-4 py-3">{row.description || '—'}</td>
                                <td className="px-4 py-3">{formatMoney(row.running_balance, row.currency)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
