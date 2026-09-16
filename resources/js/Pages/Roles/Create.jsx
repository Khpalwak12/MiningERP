import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import Checkbox from '@/Components/Checkbox';
import { asArray } from '@/utils/resource';

export default function Create({ permissions }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({ name: '', permissions: [] });

    const toggle = (perm) => {
        setData('permissions', data.permissions.includes(perm) ? data.permissions.filter((p) => p !== perm) : [...data.permissions, perm]);
    };

    return (
        <ErpLayout>
            <Head title={t('roles.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('roles.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('roles.store')); }} className="mx-auto w-full max-w-3xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
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
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('roles.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
