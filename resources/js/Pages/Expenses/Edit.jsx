import Checkbox from '@/Components/Checkbox';
import ExpensesNav from '@/Components/Erp/ExpensesNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { resourceData, resourceItems } from '@/utils/resource';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Edit({ expense, categories }) {
    const { t } = useTranslation();
    const e = resourceData(expense);
    const { data, setData, put, processing, errors } = useForm({
        expense_date: e.expense_date_shamsi || '',
        expense_category_id: e.expense_category_id || '',
        subcategory: e.subcategory || '',
        bill_number: e.bill_number || '',
        amount: e.amount ?? '',
        description: e.description || '',
        is_for_contractor: e.is_for_contractor || false,
    });
    const categoryList = resourceItems(categories);

    return (
        <ErpLayout>
            <Head title={t('expenses.edit')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('expenses.edit')}</h1>
            <ExpensesNav active="expenses.index" />

            <form onSubmit={(ev) => { ev.preventDefault(); put(route('expenses.update', e.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.expense_date} onChange={(v) => setData('expense_date', v)} error={errors.expense_date} required />
                <div>
                    <InputLabel value={t('fields.category')} required />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.expense_category_id} onChange={(ev) => setData('expense_category_id', ev.target.value)}>
                        {categoryList.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                    <InputError message={errors.expense_category_id} />
                </div>
                <div>
                    <InputLabel value={t('fields.subcategory')} />
                    <TextInput
                        className="mt-1 block w-full"
                        value={data.subcategory}
                        onChange={(ev) => setData('subcategory', ev.target.value)}
                    />
                    <InputError message={errors.subcategory} />
                </div>
                <div>
                    <InputLabel value={t('fields.bill_number')} />
                    <TextInput
                        className="mt-1 block w-full"
                        value={data.bill_number}
                        onChange={(ev) => setData('bill_number', ev.target.value)}
                    />
                    <InputError message={errors.bill_number} />
                </div>
                <div>
                    <InputLabel value={t('fields.amount')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(ev) => setData('amount', ev.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(ev) => setData('description', ev.target.value)} />
                    <InputError message={errors.description} />
                </div>
                <div className="rounded-md border border-gray-200 p-4">
                    <label className="flex items-center gap-2">
                        <Checkbox
                            checked={data.is_for_contractor}
                            onChange={(ev) => setData('is_for_contractor', ev.target.checked)}
                        />
                        <span>{t('expenses.for_contractor')}</span>
                    </label>
                    <p className="mt-2 text-sm text-gray-500">{t('expenses.for_contractor_hint')}</p>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('expenses.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
