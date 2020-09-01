<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="item in items" :value="item.id"
                    :selected="item.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (window.library.icons[item.icon] ? window.library.icons[item.icon].url : '') + '>' + ' ' + item.id + ' ' + item.name_i18n.en + (item.is_enabled ? '' : ' (disabled)')">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["items", "value"],

        data() {
            return {
                window: window,
                selected: undefined
            };
        },

        mounted() {
            this.selected = this.$props.value;
        },

        updated() {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.selected = value;
            }
        },
    };
</script>

<style scoped>

</style>
