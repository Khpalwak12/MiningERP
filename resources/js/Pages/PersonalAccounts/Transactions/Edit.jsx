import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { PERSONAL_CURRENCIES, PERSONAL_TRANSACTION_TYPES } from '@/config/personalAccountConfig';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Edit({ transaction }) {
    const { t } = useTranslation();
    const tx = resourceData(transaction);
    const { data, setData, put, processing, errors } = useForm({
        transaction_date: tx.transaction_date_shamsi || '',
        transaction_type: tx.transaction_type,
        currency: tx.currency,
        amount: tx.amount,
        description: tx.description || '',
    });

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.edit_transaction')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('personal_accounts.edit_transaction')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('personal-accounts.transactions.update', tx.id)); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.transaction_date} onChange={(v) => setData('transaction_date', v)} error={errors.transaction_date} required />
                <div>
                    <InputLabel value={t('fields.type')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.transaction_type} onChange={(e) => setData('transaction_type', e.target.value)}>
                        {PERSONAL_TRANSACTION_TYPES.map((type) => <option key={type} value={type}>{t(`personal_transaction_types.${type}`)}</option>)}
                    </select>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel value={t('fields.currency')} />
                        <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.currency} onChange={(e) => setData('currency', e.target.value)}>
                            {PERSONAL_CURRENCIES.map((c) => <option key={c} value={c}>{t(`currencies.${c}`)}</option>)}
                        </select>
                    </div>
                    <div>
                        <InputLabel value={t('fields.amount')} />
                        <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                        <InputError message={errors.amount} />
                    </div>
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.contacts.show', tx.personal_contact_id)}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
