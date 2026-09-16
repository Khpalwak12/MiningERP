import Checkbox from '@/Components/Checkbox';
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
        name: '', father_name: '', phone: '', position: '', salary: '', joining_date: today, end_date: '', status: 'active',
        is_shared_with_contractor: false, contractor_salary_share_percent: 50,
    });

    return (
        <ErpLayout>
            <Head title={t('employees.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('employees.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('employees.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
                <div><InputLabel value={t('fields.father_name')} /><TextInput className="mt-1 block w-full" value={data.father_name} onChange={(e) => setData('father_name', e.target.value)} /></div>
                <div><InputLabel value={t('fields.phone')} /><TextInput className="mt-1 block w-full" value={data.phone} onChange={(e) => setData('phone', e.target.value)} /></div>
                <div><InputLabel value={t('fields.position')} /><TextInput className="mt-1 block w-full" value={data.position} onChange={(e) => setData('position', e.target.value)} /></div>
                <div><InputLabel value={t('fields.salary')} required /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.salary} onChange={(e) => setData('salary', e.target.value)} /><InputError message={errors.salary} /></div>
                <div className="rounded-md border border-gray-200 p-4">
                    <label className="flex items-center gap-2">
                        <Checkbox
                            checked={data.is_shared_with_contractor}
                            onChange={(e) => setData('is_shared_with_contractor', e.target.checked)}
                        />
                        <span>{t('employees.shared_with_contractor')}</span>
                    </label>
                    <p className="mt-2 text-sm text-gray-500">{t('employees.shared_with_contractor_hint')}</p>
                    {data.is_shared_with_contractor && (
                        <div className="mt-3">
                            <InputLabel value={t('employees.contractor_salary_share')} required />
                            <TextInput
                                type="number"
                                step="0.01"
                                min="0.01"
                                max="100"
                                className="mt-1 block w-full max-w-xs"
                                value={data.contractor_salary_share_percent}
                                onChange={(e) => setData('contractor_salary_share_percent', e.target.value)}
                            />
                            <InputError message={errors.contractor_salary_share_percent} />
                        </div>
                    )}
                </div>
                <ShamsiDateInput label={t('fields.joining_date')} value={data.joining_date} onChange={(v) => setData('joining_date', v)} error={errors.joining_date} />
                <div>
                    <ShamsiDateInput label={t('fields.employee_end_date')} value={data.end_date} onChange={(v) => setData('end_date', v)} error={errors.end_date} />
                    <p className="mt-1 text-sm text-gray-500">{t('employees.end_date_hint')}</p>
                </div>
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('status.active')}</option>
                        <option value="inactive">{t('status.inactive')}</option>
                    </select>
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('employees.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
