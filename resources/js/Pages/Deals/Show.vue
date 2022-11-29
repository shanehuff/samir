<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';

defineProps({
  deals: Array
})
</script>

<template>
  <AppLayout title="Profile">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">
        Deals
      </h2>
    </template>

    <div class="p-6 max-w-7xl mx-auto">
      <div class="grid gap-6 mb-8">
        <div v-for="deal in deals"
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex items-center">
            <div>
              🎉
              <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                {{ deal.readable_time }}
              </span>
              <span class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                we earned
              </span>
              <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                💰{{ deal.net_profit }} ₫
              </span>
              <span class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                in
              </span>
              <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                ⏱{{ deal.duration }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>