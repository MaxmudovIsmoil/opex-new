<template>
    <div class="col-md-6 mb-4">
        <div class="bg-shadow">
            <div class="width-100 mb-4">
                <b>{{ $t('order_type') }}: {{ title }}</b>
                <button 
                  class="btn btn-outline-primary btn-sm float-end" 
                  @click.stop="$emit('create-btn')"
                >
                  <font-awesome-icon :icon="['fas', 'plus']" />  
                  {{ $t('add') }}
                </button>
            </div>

            <table class="table table-striped table-sm table-hover table-responsive" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">{{ $t('stage') }}</th>
                        <th>{{ $t('instance') }}</th>
                        <th>{{ $t('instance_users') }}</th>
                        <th class="text-end">{{ $t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tableRow
                      v-for="(roadMapChild, index) in roadMap"
                      :key="index" 
                      :roadMapChild="roadMapChild"
                      @click="$emit('edit-btn', roadMapChild)"
                    />
                    <!-- @delete-btn="$emit('delete-btn', roadMapChild)" -->
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import tableRow from './tableRow.vue';

export default {
    name: 'GridCard',
    components: { tableRow },
    props: {
        roadMap: {
            type: Array,
            required: true
        },
        title: {
            type: String,
            required: true
        }
    },
    emits: ['create-btn', 'edit-btn', 'delete-btn']
}


</script>

<style scoped>
.bg-shadow {
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    padding: 20px;
    border-radius: 5px;
}
</style>
