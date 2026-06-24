import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Create() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        start_date: '',
        end_date: '',
        status: 'active',
        notes: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('financial-years.store'));
    };

    return (
        <ErpLayout>
            <Head title={t('financial_years.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('financial_years.create')}</h1>
            <form onSubmit={submit} className="max-w-xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div>
                    <InputLabel value={t('fields.year_name')} />
                    <TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    <InputError message={errors.name} className="mt-1" />
                </div>
                <ShamsiDateInput label={t('fields.start_date')} value={data.start_date} onChange={(v) => setData('start_date', v)} error={errors.start_date} required />
                <ShamsiDateInput label={t('fields.end_date')} value={data.end_date} onChange={(v) => setData('end_date', v)} error={errors.end_date} required />
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('financial_years.statuses.active')}</option>
                        <option value="closed">{t('financial_years.statuses.closed')}</option>
                    </select>
                    <InputError message={errors.status} className="mt-1" />
                </div>
                <div>
                    <InputLabel value={t('fields.notes')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows={3} value={data.notes} onChange={(e) => setData('notes', e.target.value)} />
                    <InputError message={errors.notes} className="mt-1" />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('financial-years.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
