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
import { asArray, resourceData, resourceItems } from '@/utils/resource';

export default function Edit({ user, roles }) {
    const { t } = useTranslation();
    const u = resourceData(user);
    const { data, setData, put, processing, errors } = useForm({
        name: u.name, email: u.email, password: '', password_confirmation: '', locale: u.locale || 'en', roles: asArray(u.roles),
    });

    const toggleRole = (role) => {
        setData('roles', data.roles.includes(role) ? data.roles.filter((r) => r !== role) : [...data.roles, role]);
    };

    return (
        <ErpLayout>
            <Head title={t('users.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('users.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('users.update', u.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
                <div><InputLabel value={t('fields.email')} /><TextInput type="email" className="mt-1 block w-full" value={data.email} onChange={(e) => setData('email', e.target.value)} /><InputError message={errors.email} /></div>
                <div><InputLabel value={t('fields.password')} /><TextInput type="password" className="mt-1 block w-full" value={data.password} onChange={(e) => setData('password', e.target.value)} placeholder="Leave blank to keep" /><InputError message={errors.password} /></div>
                <div><InputLabel value={t('fields.password_confirmation')} /><TextInput type="password" className="mt-1 block w-full" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.locale')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.locale} onChange={(e) => setData('locale', e.target.value)}>
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
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('users.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
