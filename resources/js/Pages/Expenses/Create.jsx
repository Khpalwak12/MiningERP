import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceItems } from '@/utils/resource';

export default function Create({ categories }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        expense_date: '',
        expense_category_id: '',
        subcategory: '',
        bill_number: '',
        amount: '',
        description: '',
    });

    const categoryList = resourceItems(categories);

    return (
        <ErpLayout>
            <Head title={t('expenses.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('expenses.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('expenses.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.expense_date} onChange={(v) => setData('expense_date', v)} error={errors.expense_date} required />
                <div>
                    <InputLabel value={t('fields.category')} required />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.expense_category_id} onChange={(e) => setData('expense_category_id', e.target.value)}>
                        <option value="">--</option>
                        {categoryList.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                    <InputError message={errors.expense_category_id} />
                </div>
                <div>
                    <InputLabel value={t('fields.subcategory')} />
                    <TextInput
                        className="mt-1 block w-full"
                        value={data.subcategory}
                        onChange={(e) => setData('subcategory', e.target.value)}
                        placeholder={t('fields.subcategory')}
                    />
                    <InputError message={errors.subcategory} />
                </div>
                <div>
                    <InputLabel value={t('fields.bill_number')} />
                    <TextInput
                        className="mt-1 block w-full"
                        value={data.bill_number}
                        onChange={(e) => setData('bill_number', e.target.value)}
                        placeholder="INV-001"
                    />
                    <InputError message={errors.bill_number} />
                </div>
                <div>
                    <InputLabel value={t('fields.amount')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                    <InputError message={errors.description} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('expenses.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
