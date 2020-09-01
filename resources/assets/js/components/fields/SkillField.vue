<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="skill in skills" :value="skill.id"
                    :selected="skill.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (library.icons[skill.icon] ? library.icons[skill.icon].url : '') + '>' + ' ' + (skill.prefix ? (skill.prefix + ' - ') : '') + window.getPropertyByPath(skill, 'name_i18n.en|label') + ' (' + window.getPropertyByPath(skill, 'effect.name') + ')'">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["skills", "value"],

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
