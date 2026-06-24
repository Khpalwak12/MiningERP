import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import EmployeeSalarySummary from '@/Components/Erp/EmployeeSalarySummary';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceItems } from '@/utils/resource';

export default function Create({ employees }) {
    const { t } = useTranslation();
    const { canCreateTransactions } = useFinancialYearMode();
    const employeeList = resourceItems(employees);
    const { data, setData, post, processing, errors } = useForm({
        employee_id: '', payment_date: '', amount: '', payment_type: 'partial', period_month: '', notes: '',
    });

    const selectedEmployee = employeeList.find((emp) => String(emp.id) === String(data.employee_id));

    return (
        <ErpLayout>
            <Head title={t('payroll.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('payroll.create')}</h1>

            {!canCreateTransactions && (
                <div className="mb-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {t('financial_years.all_years_read_only')}
                </div>
            )}

            {selectedEmployee && (
                <div className="mb-6">
                    <EmployeeSalarySummary employee={selectedEmployee} compact />
                </div>
            )}

            <form onSubmit={(e) => { e.preventDefault(); post(route('payroll.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <InputError message={errors.financial_year} className="mb-2" />
                <div>
                    <InputLabel value={t('fields.employee')} required />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.employee_id} onChange={(e) => setData('employee_id', e.target.value)} disabled={!canCreateTransactions}>
                        <option value="">--</option>
                        {employeeList.map((emp) => <option key={emp.id} value={emp.id}>{emp.name}</option>)}
                    </select>
                    <InputError message={errors.employee_id} />
                </div>
                <ShamsiDateInput label={t('fields.date')} value={data.payment_date} onChange={(v) => setData('payment_date', v)} error={errors.payment_date} required />
                <div>
                    <InputLabel value={t('fields.amount')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} disabled={!canCreateTransactions} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.payment_type')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.payment_type} onChange={(e) => setData('payment_type', e.target.value)} disabled={!canCreateTransactions}>
                        {['full_salary', 'advance', 'partial'].map((pt) => <option key={pt} value={pt}>{t(`payment_types.${pt}`)}</option>)}
                    </select>
                    <InputError message={errors.payment_type} />
                </div>
                <div>
                    <InputLabel value={t('fields.period_month')} />
                    <TextInput placeholder="1405/06" className="mt-1 block w-full" value={data.period_month} onChange={(e) => setData('period_month', e.target.value)} disabled={!canCreateTransactions} />
                    <InputError message={errors.period_month} />
                </div>
                <div>
                    <InputLabel value={t('fields.notes')} />
                    <TextInput className="mt-1 block w-full" value={data.notes} onChange={(e) => setData('notes', e.target.value)} disabled={!canCreateTransactions} />
                    <InputError message={errors.notes} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing || !canCreateTransactions}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('payroll.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
