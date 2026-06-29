import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { PERSONAL_CONTACT_TYPES } from '@/config/personalAccountConfig';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Edit({ contact }) {
    const { t } = useTranslation();
    const c = resourceData(contact);
    const { data, setData, put, processing, errors } = useForm({
        name: c.name, phone: c.phone || '', contact_type: c.contact_type, notes: c.notes || '', status: c.status,
    });

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.edit_contact')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('personal_accounts.edit_contact')}</h1>
            <PersonalAccountsNav active="personal-accounts.contacts.index" />

            <form onSubmit={(e) => { e.preventDefault(); put(route('personal-accounts.contacts.update', c.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /></div>
                <div><InputLabel value={t('fields.phone')} /><TextInput className="mt-1 block w-full" value={data.phone} onChange={(e) => setData('phone', e.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.type')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.contact_type} onChange={(e) => setData('contact_type', e.target.value)}>
                        {PERSONAL_CONTACT_TYPES.map((type) => <option key={type} value={type}>{t(`personal_contact_types.${type}`)}</option>)}
                    </select>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.contacts.show', c.id)}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
