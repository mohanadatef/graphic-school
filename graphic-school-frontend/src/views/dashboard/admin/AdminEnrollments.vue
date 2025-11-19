<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold">التسجيلات والمدفوعات</h2>
        <p class="text-sm text-slate-500">قبول الطلبات وتحديث حالة الدفع.</p>
      </div>
      <button class="px-4 py-2 border rounded-md" @click="loadEnrollments">تحديث</button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">الطالب</th>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">الدفع</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">حضور</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="enrollment in enrollments" :key="enrollment.id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ enrollment.student?.name }}</td>
            <td class="px-4 py-3">{{ enrollment.course?.title }}</td>
            <td class="px-4 py-3">{{ paymentLabels[enrollment.payment_status] }}</td>
            <td class="px-4 py-3">{{ statusLabels[enrollment.status] }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full text-xs" :class="enrollment.can_attend ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ enrollment.can_attend ? 'مسموح' : 'موقوف' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <button class="text-primary" @click="openModal(enrollment)">تعديل</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-md">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-lg font-semibold">تعديل التسجيل</h3>
        <div>
          <label class="label">حالة الدفع</label>
          <select v-model="form.payment_status" class="input">
            <option value="not_paid">لم يدفع</option>
            <option value="partial">دفع جزئي</option>
            <option value="paid">مدفوع بالكامل</option>
          </select>
        </div>
        <div>
          <label class="label">المبلغ المدفوع</label>
          <input v-model.number="form.paid_amount" type="number" class="input" />
        </div>
        <div>
          <label class="label">حالة الطلب</label>
          <select v-model="form.status" class="input">
            <option value="pending">معلق</option>
            <option value="approved">مقبول</option>
            <option value="rejected">مرفوض</option>
          </select>
        </div>
        <div>
          <label class="label">السماح بالحضور</label>
          <select v-model="form.can_attend" class="input">
            <option :value="true">نعم</option>
            <option :value="false">لا</option>
          </select>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">حفظ</button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../../api';

const enrollments = ref([]);
const dialogRef = ref(null);
const form = reactive({
  id: null,
  payment_status: 'not_paid',
  paid_amount: 0,
  status: 'pending',
  can_attend: false,
});

const paymentLabels = {
  not_paid: 'لم يدفع',
  partial: 'دفع جزئي',
  paid: 'مدفوع',
};

const statusLabels = {
  pending: 'معلق',
  approved: 'مقبول',
  rejected: 'مرفوض',
};

async function loadEnrollments() {
  const { data } = await api.get('/admin/enrollments');
  enrollments.value = data;
}

function openModal(enrollment) {
  form.id = enrollment.id;
  form.payment_status = enrollment.payment_status;
  form.paid_amount = enrollment.paid_amount;
  form.status = enrollment.status;
  form.can_attend = enrollment.can_attend;
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

async function submit() {
  await api.put(`/admin/enrollments/${form.id}`, form);
  closeModal();
  loadEnrollments();
}

onMounted(loadEnrollments);
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.15rem;
}
</style>

