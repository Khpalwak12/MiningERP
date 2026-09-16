import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData, resourceItems } from '@/utils/resource';

export default function Edit({ payment, employees }) {
    const { t } = useTranslation();
    const p = resourceData(payment);
    const { data, setData, put, processing, errors } = useForm({
        employee_id: p.employee_id, payment_date: p.payment_date_shamsi, amount: p.amount,
        payment_type: p.payment_type, period_month: p.period_month || '', notes: p.notes || '',
    });

    return (
        <ErpLayout>
            <Head title={t('payroll.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('payroll.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('payroll.update', p.id)); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div>
                    <InputLabel value={t('fields.employee')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.employee_id} onChange={(e) => setData('employee_id', e.target.value)}>
                        {resourceItems(employees).map((emp) => <option key={emp.id} value={emp.id}>{emp.name}</option>)}
                    </select>
                </div>
                <ShamsiDateInput label={t('fields.date')} value={data.payment_date} onChange={(v) => setData('payment_date', v)} error={errors.payment_date} />
                <div><InputLabel value={t('fields.amount')} /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} /></div>
                <div>
                    <InputLabel value={t('fields.payment_type')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.payment_type} onChange={(e) => setData('payment_type', e.target.value)}>
                        {['full_salary', 'advance', 'partial'].map((pt) => <option key={pt} value={pt}>{t(`payment_types.${pt}`)}</option>)}
                    </select>
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('payroll.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
