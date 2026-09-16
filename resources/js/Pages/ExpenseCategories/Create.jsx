import ExpensesNav from '@/Components/Erp/ExpensesNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Create() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name_en: '',
        name_ps: '',
        description: '',
    });

    return (
        <ErpLayout>
            <Head title={t('expense_categories.create')} />
            <FlashMessage />
            <h1 className="mb-2 text-center text-2xl font-bold text-slate-800">{t('expense_categories.create')}</h1>
            <ExpensesNav active="expense-categories.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('expense-categories.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div>
                    <InputLabel value={t('expense_categories.name_en')} required />
                    <TextInput className="mt-1 block w-full" value={data.name_en} onChange={(e) => setData('name_en', e.target.value)} />
                    <InputError message={errors.name_en} />
                </div>
                <div>
                    <InputLabel value={t('expense_categories.name_ps')} required />
                    <TextInput className="mt-1 block w-full" value={data.name_ps} onChange={(e) => setData('name_ps', e.target.value)} dir="rtl" />
                    <InputError message={errors.name_ps} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                    <InputError message={errors.description} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('expense-categories.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
