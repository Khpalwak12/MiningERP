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
            <h1 className="mb-2 text-2xl font-bold">{t('personal_accounts.edit_home_expense')}</h1>
            <PersonalAccountsNav active="personal-accounts.home-expenses.index" />

            <form onSubmit={(ev) => { ev.preventDefault(); put(route('personal-accounts.home-expenses.update', e.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.expense_date} onChange={(v) => setData('expense_date', v)} error={errors.expense_date} required />
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.item_name} onChange={(ev) => setData('item_name', ev.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.amount')} />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(ev) => setData('amount', ev.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(ev) => setData('description', ev.target.value)} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.home-expenses.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
