<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="prefab in prefabs" :value="prefab" :selected="prefab === selected" :data-content="'<img style=\'height: 20px;\' src=\'/img/unity.png\'> ' + prefab">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                selected: undefined,
                prefabs: window.library.prefabs
            };
        },

        mounted() {
            this.selected = this.$props.value;
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        props: ["value"],

        updated() {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
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
