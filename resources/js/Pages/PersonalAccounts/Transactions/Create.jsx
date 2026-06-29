import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { PERSONAL_CURRENCIES, PERSONAL_TRANSACTION_TYPES } from '@/config/personalAccountConfig';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Create({ contact }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        transaction_date: '',
        transaction_type: 'credit',
        currency: 'AFN',
        amount: '',
        description: '',
    });

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.add_transaction')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('personal_accounts.add_transaction')} — {contact.name}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('personal-accounts.contacts.transactions.store', contact.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.transaction_date} onChange={(v) => setData('transaction_date', v)} error={errors.transaction_date} required />
                <div>
                    <InputLabel value={t('fields.type')} required />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.transaction_type} onChange={(e) => setData('transaction_type', e.target.value)}>
                        {PERSONAL_TRANSACTION_TYPES.map((type) => <option key={type} value={type}>{t(`personal_transaction_types.${type}`)}</option>)}
                    </select>
                    <InputError message={errors.transaction_type} />
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel value={t('fields.currency')} required />
                        <select className="mt-1 block w-full rounded-md border-gray-300" value={data.currency} onChange={(e) => setData('currency', e.target.value)}>
                            {PERSONAL_CURRENCIES.map((c) => <option key={c} value={c}>{t(`currencies.${c}`)}</option>)}
                        </select>
                    </div>
                    <div>
                        <InputLabel value={t('fields.amount')} required />
                        <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                        <InputError message={errors.amount} />
                    </div>
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.contacts.show', contact.id)}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
