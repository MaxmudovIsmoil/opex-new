<template>    

    <div class="d-flex justify-content-end">
        <div class="mr-2">
            <input type="text" class="form-control form-control-sm" :placeholder="$t('search')" aria-label="Search" aria-describedby="button-addon2">
        </div>
        <div class="basis-0xs">
            <button class="btn btn-sm btn-primary">
                <font-awesome-icon :icon="['fas', 'search']" /> &nbsp; {{ $t('search') }}
            </button>
        </div>
    </div>

</template>

<script>
    export default {
        
    };
</script>