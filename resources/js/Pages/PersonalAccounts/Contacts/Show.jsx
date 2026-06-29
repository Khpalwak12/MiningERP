import ActionButtons from '@/Components/Erp/ActionButtons';
import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { formatMoney } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ contact, balances, ledger }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions } = useFinancialYearMode();
    const c = resourceData(contact);

    return (
        <ErpLayout>
            <Head title={c.name} />
            <FlashMessage />
            <PersonalAccountsNav active="personal-accounts.contacts.index" />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{c.name}</h1>
                <div className="flex gap-2">
                    {canCreateTransactions && can('personal-accounts.create') && (
                        <Link href={route('personal-accounts.contacts.transactions.create', c.id)}>
                            <PrimaryButton>{t('personal_accounts.add_transaction')}</PrimaryButton>
                        </Link>
                    )}
                    <Link href={route('personal-accounts.contacts.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
                </div>
            </div>

            <div className="mb-6 grid gap-4 sm:grid-cols-2">
                <div className="rounded-lg bg-white p-4 shadow">
                    <p className="text-sm text-gray-500">{t('personal_accounts.balance_afn')}</p>
                    <p className="text-xl font-bold text-amber-700">{formatMoney(balances?.AFN, 'AFN')}</p>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <p className="text-sm text-gray-500">{t('personal_accounts.balance_usd')}</p>
                    <p className="text-xl font-bold text-amber-700">{formatMoney(balances?.USD, 'USD')}</p>
                </div>
            </div>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <h2 className="border-b px-4 py-3 font-semibold">{t('personal_accounts.ledger')}</h2>
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.currency')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_transaction_types.credit')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_transaction_types.payment')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.running_balance')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {!ledger?.length && <tr><td colSpan={8} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {ledger?.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.date_shamsi}</td>
                                <td className="px-4 py-3">{t(`personal_transaction_types.${row.type}`)}</td>
                                <td className="px-4 py-3">{t(`currencies.${row.currency}`)}</td>
                                <td className="px-4 py-3">{row.description || '—'}</td>
                                <td className="px-4 py-3">{row.credit != null ? formatMoney(row.credit, row.currency) : '—'}</td>
                                <td className="px-4 py-3">{row.payment != null ? formatMoney(row.payment, row.currency) : '—'}</td>
                                <td className="px-4 py-3 font-medium">{formatMoney(row.running_balance, row.currency)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('personal-accounts.edit') && !row.is_locked ? route('personal-accounts.transactions.edit', row.id) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
