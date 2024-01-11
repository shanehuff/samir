<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';

defineProps({
  deals: Array
})
</script>

<template>
  <AppLayout title="Monthly ROI">
    <template #header>
      <div class="flex justify-center">
        <h2 class="text-xl text-gray-800 leading-tight dark:text-white">
          Monthly Income 📊
        </h2>
      </div>
    </template>

    <div class="p-6 max-w-7xl mx-auto">
      <div class="grid gap-6 mb-8">
        <div v-for="deal in deals"
             class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex justify-between">
              <span class="text-sm font-semibold text-gray-400 dark:text-gray-400">
                🗓 {{ deal.month }}
              </span>

              <span class="text-sm font-semibold text-green-600 dark:text-green-300">
                  <span v-if="parseInt(deal.net_profit) > 0" class="text-green-700 dark:text-green-500">
                      + {{ deal.net_profit }} 💰
                  </span>

                  <span v-else class="text-red-700 dark:text-red-500">
                    {{ deal.net_profit }} 💸
                  </span>
              </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>