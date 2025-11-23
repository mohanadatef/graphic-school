import { describe, it, expect, beforeEach } from 'vitest';
import { mountWithPlugins } from '../utils/test-utils';
import FilterDropdown from '../../src/components/common/FilterDropdown.vue';

describe('FilterDropdown', () => {
  const defaultOptions = [
    { id: 1, name: 'Option 1' },
    { id: 2, name: 'Option 2' },
    { id: 3, name: 'Option 3' },
  ];

  it('should render with options', () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: defaultOptions,
        placeholder: 'Select option',
      },
    });

    expect(wrapper.find('select').exists()).toBe(true);
    expect(wrapper.findAll('option')).toHaveLength(4); // 3 options + placeholder
  });

  it('should display placeholder when no value selected', () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: defaultOptions,
        placeholder: 'Select option',
      },
    });

    const select = wrapper.find('select');
    expect(select.element.value).toBe('');
    expect(select.find('option[value=""]').text()).toBe('Select option');
  });

  it('should display selected value', async () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: 2,
        options: defaultOptions,
      },
    });

    const select = wrapper.find('select');
    expect(select.element.value).toBe('2');
  });

  it('should emit update:modelValue when selection changes', async () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: defaultOptions,
      },
    });

    const select = wrapper.find('select');
    await select.setValue('2');

    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')[0]).toEqual([2]);
  });

  it('should handle string values', async () => {
    const stringOptions = ['option1', 'option2', 'option3'];
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: stringOptions,
      },
    });

    const select = wrapper.find('select');
    await select.setValue('option2');

    expect(wrapper.emitted('update:modelValue')[0]).toEqual(['option2']);
  });

  it('should use custom valueKey and labelKey', () => {
    const customOptions = [
      { value: 1, label: 'Custom 1' },
      { value: 2, label: 'Custom 2' },
    ];

    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: customOptions,
        valueKey: 'value',
        labelKey: 'label',
      },
    });

    const options = wrapper.findAll('option');
    expect(options[1].text()).toBe('Custom 1');
    expect(options[2].text()).toBe('Custom 2');
  });

  it('should apply active class when value is selected', () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: 2,
        options: defaultOptions,
      },
    });

    const select = wrapper.find('select');
    expect(select.classes()).toContain('filter-dropdown-active');
  });
});

