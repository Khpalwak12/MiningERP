import Checkbox from '@/Components/Checkbox';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData } from '@/utils/resource';

export default function Edit({ employee }) {
    const { t } = useTranslation();
    const e = resourceData(employee);
    const { data, setData, put, processing, errors } = useForm({
        name: e.name, father_name: e.father_name || '', phone: e.phone || '', position: e.position || '',
        salary: e.salary, joining_date: e.joining_date_shamsi || '', status: e.status,
        is_shared_with_contractor: e.is_shared_with_contractor || false,
        contractor_salary_share_percent: e.contractor_salary_share_percent ?? 50,
    });

    return (
        <ErpLayout>
            <Head title={t('employees.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('employees.edit')}</h1>

            <form onSubmit={(ev) => { ev.preventDefault(); put(route('employees.update', e.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.name} onChange={(ev) => setData('name', ev.target.value)} /></div>
                <div><InputLabel value={t('fields.father_name')} /><TextInput className="mt-1 block w-full" value={data.father_name} onChange={(ev) => setData('father_name', ev.target.value)} /></div>
                <div><InputLabel value={t('fields.phone')} /><TextInput className="mt-1 block w-full" value={data.phone} onChange={(ev) => setData('phone', ev.target.value)} /></div>
                <div><InputLabel value={t('fields.position')} /><TextInput className="mt-1 block w-full" value={data.position} onChange={(ev) => setData('position', ev.target.value)} /></div>
                <div><InputLabel value={t('fields.salary')} /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.salary} onChange={(ev) => setData('salary', ev.target.value)} /></div>
                <div className="rounded-md border border-gray-200 p-4">
                    <label className="flex items-center gap-2">
                        <Checkbox
                            checked={data.is_shared_with_contractor}
                            onChange={(ev) => setData('is_shared_with_contractor', ev.target.checked)}
                        />
                        <span>{t('employees.shared_with_contractor')}</span>
                    </label>
                    <p className="mt-2 text-sm text-gray-500">{t('employees.shared_with_contractor_hint')}</p>
                    {data.is_shared_with_contractor && (
                        <div className="mt-3">
                            <InputLabel value={t('employees.contractor_salary_share')} />
                            <TextInput
                                type="number"
                                step="0.01"
                                min="0.01"
                                max="100"
                                className="mt-1 block w-full max-w-xs"
                                value={data.contractor_salary_share_percent}
                                onChange={(ev) => setData('contractor_salary_share_percent', ev.target.value)}
                            />
                            <InputError message={errors.contractor_salary_share_percent} />
                        </div>
                    )}
                </div>
                <ShamsiDateInput label={t('fields.joining_date')} value={data.joining_date} onChange={(v) => setData('joining_date', v)} error={errors.joining_date} />
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(ev) => setData('status', ev.target.value)}>
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
