import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Edit({ expense }) {
    const { t } = useTranslation();
    const e = resourceData(expense);
    const { data, setData, put, processing, errors } = useForm({
        expense_date: e.expense_date_shamsi || '',
        item_name: e.item_name,
        amount: e.amount,
        description: e.description || '',
    });

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.edit_home_expense')} />
            <FlashMessage />
            <h1 className="mb-2 text-center text-2xl font-bold text-slate-800">{t('personal_accounts.edit_home_expense')}</h1>
            <PersonalAccountsNav active="personal-accounts.home-expenses.index" />

            <form onSubmit={(ev) => { ev.preventDefault(); put(route('personal-accounts.home-expenses.update', e.id)); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.expense_date} onChange={(v) => setData('expense_date', v)} error={errors.expense_date} required />
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.item_name} onChange={(ev) => setData('item_name', ev.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.amount')} />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(ev) => setData('amount', ev.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.description} onChange={(ev) => setData('description', ev.target.value)} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.home-expenses.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
