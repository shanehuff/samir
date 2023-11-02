<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';

defineProps({
  netProfit: Number,
  fee: Number,
  dealsCount: Number,
  avgDuration: Number,
  initCapital: Number,
  upTime: String,
  incomes: Number,
})
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">
        Dashboard
      </h2>
    </template>

    <div class="p-6 max-w-7xl mx-auto">
      <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <div
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex items-center">
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                Net Profit (VND)
              </p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ netProfit }}
              </p>
            </div>
          </div>
        </div>

        <div
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex items-center">
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                Fee & Income (VND)
              </p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                  <span class="text-red-500">{{ fee }}</span> &
                  <span class="text-green-500">{{ incomes }}</span>
              </p>
            </div>
          </div>
        </div>

        <div
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex items-center">
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                Deals
              </p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ dealsCount }}
              </p>
            </div>
          </div>
        </div>

        <div
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex items-center">
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                Up Time
              </p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ upTime }}
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>