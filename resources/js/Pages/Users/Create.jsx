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
import { resourceItems } from '@/utils/resource';

export default function Create({ roles }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '', email: '', password: '', password_confirmation: '', locale: 'en', roles: [],
    });

    const toggleRole = (role) => {
        setData('roles', data.roles.includes(role) ? data.roles.filter((r) => r !== role) : [...data.roles, role]);
    };

    return (
        <ErpLayout>
            <Head title={t('users.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('users.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('users.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
                <div><InputLabel value={t('fields.email')} required /><TextInput type="email" className="mt-1 block w-full" value={data.email} onChange={(e) => setData('email', e.target.value)} /><InputError message={errors.email} /></div>
                <div><InputLabel value={t('fields.password')} required /><TextInput type="password" className="mt-1 block w-full" value={data.password} onChange={(e) => setData('password', e.target.value)} /><InputError message={errors.password} /></div>
                <div><InputLabel value={t('fields.password_confirmation')} required /><TextInput type="password" className="mt-1 block w-full" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.locale')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.locale} onChange={(e) => setData('locale', e.target.value)}>
                        <option value="en">{t('locale.english')}</option>
                        <option value="ps">{t('locale.pashto')}</option>
                    </select>
                </div>
                <div>
                    <InputLabel value={t('fields.roles')} />
                    <div className="mt-2 space-y-2">
                        {resourceItems(roles).map((role) => (
                            <label key={role.id} className="flex items-center gap-2">
                                <Checkbox checked={data.roles.includes(role.name)} onChange={() => toggleRole(role.name)} />
                                <span>{role.name}</span>
                            </label>
                        ))}
                    </div>
                    <InputError message={errors.roles} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('users.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
