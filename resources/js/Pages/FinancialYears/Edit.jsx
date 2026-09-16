import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Edit({ financialYear }) {
    const { t } = useTranslation();
    const year = resourceData(financialYear);
    const { data, setData, put, processing, errors } = useForm({
        name: year.name || '',
        start_date: year.start_date_shamsi || '',
        end_date: year.end_date_shamsi || '',
        status: year.status || 'active',
        notes: year.notes || '',
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('financial-years.update', year.id));
    };

    return (
        <ErpLayout>
            <Head title={t('financial_years.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('financial_years.edit')}</h1>
            <form onSubmit={submit} className="mx-auto w-full max-w-xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div>
                    <InputLabel value={t('fields.year_name')} />
                    <TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    <InputError message={errors.name} className="mt-1" />
                </div>
                <ShamsiDateInput label={t('fields.start_date')} value={data.start_date} onChange={(v) => setData('start_date', v)} error={errors.start_date} required />
                <ShamsiDateInput label={t('fields.end_date')} value={data.end_date} onChange={(v) => setData('end_date', v)} error={errors.end_date} required />
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('financial_years.statuses.active')}</option>
                        <option value="closed">{t('financial_years.statuses.closed')}</option>
                    </select>
                    <InputError message={errors.status} className="mt-1" />
                </div>
                <div>
                    <InputLabel value={t('fields.notes')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows={3} value={data.notes} onChange={(e) => setData('notes', e.target.value)} />
                    <InputError message={errors.notes} className="mt-1" />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('financial-years.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
