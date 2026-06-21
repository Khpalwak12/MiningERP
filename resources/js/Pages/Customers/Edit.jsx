import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData } from '@/utils/resource';

export default function Edit({ customer }) {
    const { t } = useTranslation();
    const c = resourceData(customer);
    const { data, setData, put, processing, errors } = useForm({
        name: c.name || '', owner_name: c.owner_name || '', phone: c.phone || '', address: c.address || '', status: c.status || 'active',
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('customers.update', c.id));
    };

    return (
        <ErpLayout>
            <Head title={t('customers.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('customers.edit')}</h1>

            <form onSubmit={submit} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div>
                    <InputLabel value={t('fields.name')} required />
                    <TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    <InputError message={errors.name} />
                </div>
                <div>
                    <InputLabel value={t('fields.owner_name')} />
                    <TextInput className="mt-1 block w-full" value={data.owner_name} onChange={(e) => setData('owner_name', e.target.value)} />
                </div>
                <div>
                    <InputLabel value={t('fields.phone')} />
                    <TextInput className="mt-1 block w-full" value={data.phone} onChange={(e) => setData('phone', e.target.value)} />
                </div>
                <div>
                    <InputLabel value={t('fields.address')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="3" value={data.address} onChange={(e) => setData('address', e.target.value)} />
                </div>
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('status.active')}</option>
                        <option value="inactive">{t('status.inactive')}</option>
                    </select>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('customers.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
