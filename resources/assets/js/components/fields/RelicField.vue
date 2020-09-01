<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="relic in relics" :value="relic.id"
                    :selected="relic.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (library.icons[relic.icon] ? library.icons[relic.icon].url : '') + '>' + ' ' + window.getPropertyByPath(relic, 'name_i18n.en') + ')'">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["relics", "value"],

        data() {
            return {
                window: window,
                library: window.library,
                selected: this.$props.value
            };
        },

        mounted() {
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
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
        }
    };
</script>

<style scoped>

</style>
