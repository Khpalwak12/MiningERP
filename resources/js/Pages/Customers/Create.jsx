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
        name: '', owner_name: '', phone: '', address: '', status: 'active',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('customers.store'));
    };

    return (
        <ErpLayout>
            <Head title={t('customers.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('customers.create')}</h1>

            <form onSubmit={submit} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div>
                    <InputLabel value={t('fields.name')} required />
                    <TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    <InputError message={errors.name} />
                </div>
                <div>
                    <InputLabel value={t('fields.owner_name')} />
                    <TextInput className="mt-1 block w-full" value={data.owner_name} onChange={(e) => setData('owner_name', e.target.value)} />
                    <InputError message={errors.owner_name} />
                </div>
                <div>
                    <InputLabel value={t('fields.phone')} />
                    <TextInput className="mt-1 block w-full" value={data.phone} onChange={(e) => setData('phone', e.target.value)} />
                    <InputError message={errors.phone} />
                </div>
                <div>
                    <InputLabel value={t('fields.address')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="3" value={data.address} onChange={(e) => setData('address', e.target.value)} />
                    <InputError message={errors.address} />
                </div>
                <div>
                    <InputLabel value={t('fields.status')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        <option value="active">{t('status.active')}</option>
                        <option value="inactive">{t('status.inactive')}</option>
                    </select>
                    <InputError message={errors.status} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('customers.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
