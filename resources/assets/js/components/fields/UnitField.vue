<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="unit in units" :value="unit.id" :selected="unit.id === selected">
                {{ window.getPropertyByPath(unit, 'name_i18n.en|label') + " (" + unit.suffix + ")" }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                selected: undefined,
                window: window
            }
        },

        mounted() {
            this.selected = this.$props.value;
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
        },

        props: ["units", "value"],

        updated () {
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit('input', value);
            },

            value: function (value) {
                this.selected = value;
            }
        }
    };
</script>

<style scoped>

</style>
