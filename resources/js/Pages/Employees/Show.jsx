import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import EmployeeSalarySummary from '@/Components/Erp/EmployeeSalarySummary';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { formatCurrency } from '@/utils/format';
import { resourceData, resourceItems } from '@/utils/resource';
import { Head, Link, router, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ employee }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const today = useTodayShamsi();
    const e = resourceData(employee);
    const absences = resourceItems(e.absences);
    const canEdit = can('employees.edit');

    const { data, setData, post, processing, errors, reset } = useForm({
        absence_date: today,
        days: 1,
        notes: '',
    });

    const submitAbsence = (event) => {
        event.preventDefault();
        post(route('employees.absences.store', e.id), {
            preserveScroll: true,
            onSuccess: () => reset('days', 'notes'),
        });
    };

    return (
        <ErpLayout>
            <Head title={e.name} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{e.name}</h1>
                <div className="flex gap-2">
                    {canEdit && (
                        <Link href={route('employees.edit', e.id)}>
                            <SecondaryButton type="button">{t('employees.edit')}</SecondaryButton>
                        </Link>
                    )}
                    <Link href={route('employees.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
                </div>
            </div>

            <EmployeeSalarySummary employee={e} />

            <div className="mt-6 rounded-lg bg-white p-4 shadow text-sm">
                <h2 className="mb-3 font-semibold">{t('employees.profile_details')}</h2>
                <dl className="grid gap-2 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.father_name')}</dt><dd>{e.father_name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.phone')}</dt><dd>{e.phone}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.position')}</dt><dd>{e.position}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.status')}</dt><dd>{t(`status.${e.status}`)}</dd></div>
                    <div>
                        <dt className="text-gray-500">{t('fields.employee_end_date')}</dt>
                        <dd>{e.end_date_shamsi || e.end_date_effective_shamsi || '—'}</dd>
                    </div>
                    {e.is_shared_with_contractor && (
                        <div><dt className="text-gray-500">{t('employees.shared_with_contractor')}</dt><dd>{e.contractor_salary_share_percent}%</dd></div>
                    )}
                </dl>
            </div>

            <div className="mt-6 rounded-lg bg-white p-4 shadow">
                <h2 className="mb-3 font-semibold">{t('employees.absences')}</h2>
                <p className="mb-4 text-sm text-gray-600">
                    {t('fields.absence_deduction')}: {formatCurrency(e.absence_deduction ?? 0)}
                    {' · '}
                    {t('fields.absence_days')}: {e.absence_days ?? 0}
                </p>

                {canEdit && (
                    <form onSubmit={submitAbsence} className="mb-6 grid gap-4 rounded-md border border-gray-200 p-4 sm:grid-cols-4">
                        <div className="sm:col-span-1">
                            <ShamsiDateInput
                                label={t('fields.absence_date')}
                                value={data.absence_date}
                                onChange={(value) => setData('absence_date', value)}
                                error={errors.absence_date}
                                required
                            />
                        </div>
                        <div>
                            <InputLabel value={t('fields.days')} required />
                            <TextInput
                                type="number"
                                min="1"
                                className="mt-1 block w-full"
                                value={data.days}
                                onChange={(event) => setData('days', event.target.value)}
                            />
                            <InputError message={errors.days} />
                        </div>
                        <div className="sm:col-span-1">
                            <InputLabel value={t('fields.notes')} />
                            <TextInput
                                className="mt-1 block w-full"
                                value={data.notes}
                                onChange={(event) => setData('notes', event.target.value)}
                            />
                        </div>
                        <div className="flex items-end">
                            <PrimaryButton disabled={processing}>{t('employees.add_absence')}</PrimaryButton>
                        </div>
                    </form>
                )}

                <div className="overflow-hidden rounded-md border border-gray-100">
                    <table className="min-w-full text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-4 py-2 text-left">{t('fields.absence_date')}</th>
                                <th className="px-4 py-2 text-left">{t('fields.days')}</th>
                                <th className="px-4 py-2 text-left">{t('fields.notes')}</th>
                                {canEdit && <th className="px-4 py-2 text-right">{t('actions.column')}</th>}
                            </tr>
                        </thead>
                        <tbody className="divide-y">
                            {absences.length === 0 && (
                                <tr>
                                    <td colSpan={canEdit ? 4 : 3} className="px-4 py-4 text-center text-gray-500">
                                        {t('messages.no_records')}
                                    </td>
                                </tr>
                            )}
                            {absences.map((absence) => (
                                <tr key={absence.id}>
                                    <td className="px-4 py-2">{absence.absence_date_shamsi}</td>
                                    <td className="px-4 py-2">{absence.days}</td>
                                    <td className="px-4 py-2">{absence.notes || '—'}</td>
                                    {canEdit && (
                                        <td className="px-4 py-2 text-right">
                                            <button
                                                type="button"
                                                className="text-red-600 hover:underline"
                                                onClick={() => router.delete(route('employees.absences.destroy', [e.id, absence.id]), { preserveScroll: true })}
                                            >
                                                {t('actions.delete')}
                                            </button>
                                        </td>
                                    )}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </ErpLayout>
    );
}
