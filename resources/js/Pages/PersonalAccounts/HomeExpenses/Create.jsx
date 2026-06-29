import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Create() {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        expense_date: today, item_name: '', amount: '', description: '',
    });

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.create_home_expense')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('personal_accounts.create_home_expense')}</h1>
            <PersonalAccountsNav active="personal-accounts.home-expenses.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('personal-accounts.home-expenses.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.expense_date} onChange={(v) => setData('expense_date', v)} error={errors.expense_date} required />
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.item_name} onChange={(e) => setData('item_name', e.target.value)} placeholder={t('personal_accounts.home_expenses')} /><InputError message={errors.item_name} /></div>
                <div>
                    <InputLabel value={t('fields.amount')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('personal-accounts.home-expenses.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
