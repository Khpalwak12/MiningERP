import ActionButtons from '@/Components/Erp/ActionButtons';
import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatMoney } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import { useState } from 'react';

export default function Index({ contacts, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.contacts')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('personal_accounts.title')}</h1>
            <PersonalAccountsNav active="personal-accounts.contacts.index" />
            <PageHeader
                title={t('personal_accounts.contacts')}
                createRoute={can('personal-accounts.create') ? route('personal-accounts.contacts.create') : null}
                createLabel={t('personal_accounts.create_contact')}
            />

            <form onSubmit={(e) => { e.preventDefault(); router.get(route('personal-accounts.contacts.index'), { search }, { preserveState: true }); }} className="mb-4 flex gap-2">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.phone')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.type')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.balance_afn')}</th>
                            <th className="px-4 py-3 text-left">{t('personal_accounts.balance_usd')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {contacts?.data?.length === 0 && (
                            <tr><td colSpan={6} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {contacts?.data?.map((c) => (
                            <tr key={c.id}>
                                <td className="px-4 py-3 font-medium">{c.name}</td>
                                <td className="px-4 py-3">{c.phone || '—'}</td>
                                <td className="px-4 py-3">{t(`personal_contact_types.${c.contact_type}`)}</td>
                                <td className="px-4 py-3 text-amber-700">{formatMoney(c.balance_afn, 'AFN')}</td>
                                <td className="px-4 py-3 text-amber-700">{formatMoney(c.balance_usd, 'USD')}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('personal-accounts.contacts.show', c.id)}
                                        editHref={can('personal-accounts.edit') ? route('personal-accounts.contacts.edit', c.id) : null}
                                        onDelete={can('personal-accounts.delete') ? () => router.delete(route('personal-accounts.contacts.destroy', c.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={contacts} /></div>
            </div>
        </ErpLayout>
    );
}
