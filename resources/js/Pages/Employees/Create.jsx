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

export default function Create() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '', father_name: '', phone: '', position: '', salary: '', joining_date: '', status: 'active',
    });

    return (
        <ErpLayout>
            <Head title={t('employees.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('employees.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('employees.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
                <div><InputLabel value={t('fields.father_name')} /><TextInput className="mt-1 block w-full" value={data.father_name} onChange={(e) => setData('father_name', e.target.value)} /></div>
                <div><InputLabel value={t('fields.phone')} /><TextInput className="mt-1 block w-full" value={data.phone} onChange={(e) => setData('phone', e.target.value)} /></div>
                <div><InputLabel value={t('fields.position')} /><TextInput className="mt-1 block w-full" value={data.position} onChange={(e) => setData('position', e.target.value)} /></div>
                <div><InputLabel value={t('fields.salary')} required /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.salary} onChange={(e) => setData('salary', e.target.value)} /><InputError message={errors.salary} /></div>
                <ShamsiDateInput label={t('fields.joining_date')} value={data.joining_date} onChange={(v) => setData('joining_date', v)} error={errors.joining_date} />
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('status.active')}</option>
                        <option value="inactive">{t('status.inactive')}</option>
                    </select>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('employees.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
