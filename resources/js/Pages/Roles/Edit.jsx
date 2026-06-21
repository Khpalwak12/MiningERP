import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import Checkbox from '@/Components/Checkbox';
import { asArray, resourceData } from '@/utils/resource';

export default function Edit({ role, permissions }) {
    const { t } = useTranslation();
    const r = resourceData(role);
    const { data, setData, put, processing } = useForm({ name: r.name, permissions: asArray(r.permissions) });

    const toggle = (perm) => {
        setData('permissions', data.permissions.includes(perm) ? data.permissions.filter((p) => p !== perm) : [...data.permissions, perm]);
    };

    return (
        <ErpLayout>
            <Head title={t('roles.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('roles.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('roles.update', r.id)); }} className="max-w-3xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} disabled={r.name === 'Super Admin'} /></div>
                <div>
                    <InputLabel value={t('fields.permissions')} />
                    <div className="mt-2 max-h-64 overflow-y-auto grid grid-cols-2 gap-2 text-sm">
                        {asArray(permissions).map((perm) => (
                            <label key={perm} className="flex items-center gap-2">
                                <Checkbox checked={data.permissions.includes(perm)} onChange={() => toggle(perm)} />
                                <span>{perm}</span>
                            </label>
                        ))}
                    </div>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('roles.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
