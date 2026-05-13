@props([
    'id' => null,
    'title' => null,
    'description' => null,
    'previousLabel' => 'Kembali',
    'nextLabel' => 'Lanjut',
    'submitLabel' => 'Simpan',
])

@php
    $wizardId = $id ?? 'form-wizard-' . substr(md5(uniqid('', true)), 0, 8);
@endphp

<div
    id="{{ $wizardId }}"
    data-form-wizard
    data-previous-label="{{ $previousLabel }}"
    data-next-label="{{ $nextLabel }}"
    data-submit-label="{{ $submitLabel }}"
    {{ $attributes->class('admin-form-wizard') }}
>
    @if($title || $description)
        <div class="admin-form-wizard__intro">
            @if($title)
                <h4 class="mb-1">{{ $title }}</h4>
            @endif
            @if($description)
                <p class="text-muted mb-0">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="admin-form-wizard__track" aria-hidden="true">
        <div class="admin-form-wizard__track-bar" data-wizard-progress></div>
    </div>

    <ol class="admin-form-wizard__nav" data-wizard-nav></ol>

    <div class="admin-form-wizard__steps" data-wizard-steps>
        {{ $slot }}
    </div>

    <div class="admin-form-wizard__footer">
        <x-admin.ui.button
            type="button"
            variant="secondary"
            outline
            data-wizard-previous
        >
            {{ $previousLabel }}
        </x-admin.ui.button>

        <div class="admin-form-wizard__counter" data-wizard-counter></div>

        <x-admin.ui.button
            type="button"
            data-wizard-next
        >
            {{ $nextLabel }}
        </x-admin.ui.button>
    </div>
</div>

@once
    @push('styles')
        <style>
            .admin-form-wizard {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .admin-form-wizard__intro {
                border-bottom: 1px solid rgba(0, 0, 0, .075);
                padding-bottom: 1rem;
            }

            .admin-form-wizard__track {
                background: #eef1f5;
                border-radius: 999px;
                height: .45rem;
                overflow: hidden;
            }

            .admin-form-wizard__track-bar {
                background: linear-gradient(90deg, #3454d1, #2fb344);
                border-radius: inherit;
                height: 100%;
                transition: width .24s ease;
                width: 0;
            }

            .admin-form-wizard__nav {
                display: grid;
                gap: .75rem;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .admin-form-wizard__nav-button {
                align-items: center;
                background: #fff;
                border: 1px solid #dbe0e6;
                border-radius: .5rem;
                color: #4b5563;
                display: flex;
                gap: .75rem;
                min-height: 4rem;
                padding: .75rem;
                text-align: left;
                transition: border-color .18s ease, box-shadow .18s ease, color .18s ease;
                width: 100%;
            }

            .admin-form-wizard__nav-button:hover {
                border-color: #aeb9c7;
                color: #1f2937;
            }

            .admin-form-wizard__nav-button.is-active {
                border-color: #3454d1;
                box-shadow: 0 0 0 .2rem rgba(52, 84, 209, .12);
                color: #172554;
            }

            .admin-form-wizard__nav-button.is-complete {
                border-color: rgba(47, 179, 68, .45);
            }

            .admin-form-wizard__nav-button.has-error {
                border-color: #dc3545;
                box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .1);
            }

            .admin-form-wizard__step-number {
                align-items: center;
                background: #eef1f5;
                border-radius: 999px;
                display: inline-flex;
                flex: 0 0 2rem;
                font-size: .875rem;
                font-weight: 700;
                height: 2rem;
                justify-content: center;
                width: 2rem;
            }

            .admin-form-wizard__nav-button.is-active .admin-form-wizard__step-number {
                background: #3454d1;
                color: #fff;
            }

            .admin-form-wizard__nav-button.is-complete .admin-form-wizard__step-number {
                background: #2fb344;
                color: #fff;
            }

            .admin-form-wizard__step-title {
                display: block;
                font-weight: 700;
            }

            .admin-form-wizard__step-description {
                color: #6b7280;
                display: block;
                font-size: .8125rem;
                margin-top: .15rem;
            }

            .admin-form-wizard.is-enhanced [data-wizard-step][hidden] {
                display: none !important;
            }

            .admin-form-wizard__step-heading {
                align-items: flex-start;
                display: flex;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            .admin-form-wizard__footer {
                align-items: center;
                border-top: 1px solid rgba(0, 0, 0, .075);
                display: flex;
                gap: 1rem;
                justify-content: space-between;
                padding-top: 1rem;
            }

            .admin-form-wizard__counter {
                color: #6b7280;
                font-size: .875rem;
                font-weight: 600;
            }

            @media (max-width: 575.98px) {
                .admin-form-wizard__nav {
                    grid-template-columns: 1fr;
                }

                .admin-form-wizard__footer {
                    align-items: stretch;
                    flex-direction: column;
                }

                .admin-form-wizard__counter {
                    order: -1;
                    text-align: center;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function () {
                if (window.__adminFormWizardInit) return;
                window.__adminFormWizardInit = true;

                function fieldControls(step) {
                    return Array.prototype.slice.call(step.querySelectorAll('input, select, textarea'))
                        .filter(function (control) {
                            var type = (control.getAttribute('type') || '').toLowerCase();
                            return !['hidden', 'button', 'submit', 'reset'].includes(type);
                        });
                }

                function hasStepError(step) {
                    return !!step.querySelector('.is-invalid, .invalid-feedback.d-block');
                }

                function initWizard(root) {
                    var steps = Array.prototype.slice.call(root.querySelectorAll('[data-wizard-step]'));
                    if (!steps.length) return;

                    var form = root.closest('form');
                    var nav = root.querySelector('[data-wizard-nav]');
                    var progress = root.querySelector('[data-wizard-progress]');
                    var previousButton = root.querySelector('[data-wizard-previous]');
                    var nextButton = root.querySelector('[data-wizard-next]');
                    var counter = root.querySelector('[data-wizard-counter]');
                    var previousLabel = root.dataset.previousLabel || 'Kembali';
                    var nextLabel = root.dataset.nextLabel || 'Lanjut';
                    var submitLabel = root.dataset.submitLabel || 'Simpan';
                    var originalDisabled = new WeakMap();
                    var current = Math.max(steps.findIndex(hasStepError), 0);
                    var navButtons = [];

                    root.classList.add('is-enhanced');

                    steps.forEach(function (step, index) {
                        fieldControls(step).forEach(function (control) {
                            originalDisabled.set(control, control.disabled);
                        });

                        var item = document.createElement('li');
                        var button = document.createElement('button');
                        var number = document.createElement('span');
                        var body = document.createElement('span');
                        var title = document.createElement('span');
                        var description = document.createElement('span');

                        button.type = 'button';
                        button.className = 'admin-form-wizard__nav-button';
                        number.className = 'admin-form-wizard__step-number';
                        body.className = 'admin-form-wizard__step-body';
                        title.className = 'admin-form-wizard__step-title';
                        description.className = 'admin-form-wizard__step-description';

                        number.textContent = index + 1;
                        title.textContent = step.dataset.title || ('Step ' + (index + 1));
                        description.textContent = step.dataset.description || '';

                        body.appendChild(title);
                        if (description.textContent) body.appendChild(description);
                        button.appendChild(number);
                        button.appendChild(body);
                        item.appendChild(button);
                        nav.appendChild(item);
                        navButtons.push(button);

                        button.addEventListener('click', function () {
                            if (index <= current) {
                                activate(index);
                                return;
                            }

                            for (var i = current; i < index; i++) {
                                if (!validateStep(i)) return;
                            }

                            activate(index);
                        });
                    });

                    function restoreStepControls(step) {
                        fieldControls(step).forEach(function (control) {
                            control.disabled = originalDisabled.get(control) || false;
                        });
                    }

                    function disableStepControls(step) {
                        fieldControls(step).forEach(function (control) {
                            if (!originalDisabled.get(control)) {
                                control.disabled = true;
                            }
                        });
                    }

                    function restoreAllControls() {
                        steps.forEach(restoreStepControls);
                    }

                    function activate(index) {
                        current = Math.min(Math.max(index, 0), steps.length - 1);

                        steps.forEach(function (step, stepIndex) {
                            var active = stepIndex === current;
                            step.hidden = !active;
                            if (active) {
                                restoreStepControls(step);
                            } else {
                                disableStepControls(step);
                            }
                        });

                        navButtons.forEach(function (button, buttonIndex) {
                            button.classList.toggle('is-active', buttonIndex === current);
                            button.classList.toggle('is-complete', buttonIndex < current);
                            button.classList.toggle('has-error', hasStepError(steps[buttonIndex]));
                            button.setAttribute('aria-current', buttonIndex === current ? 'step' : 'false');
                        });

                        if (progress) {
                            progress.style.width = (((current + 1) / steps.length) * 100) + '%';
                        }

                        if (previousButton) {
                            previousButton.disabled = current === 0;
                            previousButton.textContent = previousLabel;
                        }

                        if (nextButton) {
                            nextButton.textContent = current === steps.length - 1 ? submitLabel : nextLabel;
                        }

                        if (counter) {
                            counter.textContent = 'Step ' + (current + 1) + ' dari ' + steps.length;
                        }

                        if (window.jQuery && window.jQuery.fn.select2) {
                            window.jQuery(steps[current]).find('.js-select2-component').trigger('change.select2');
                        }
                    }

                    function validateStep(index) {
                        restoreStepControls(steps[index]);

                        var invalid = fieldControls(steps[index]).find(function (control) {
                            return !control.checkValidity();
                        });

                        if (!invalid) return true;

                        activate(index);
                        invalid.reportValidity();

                        return false;
                    }

                    function validateAllSteps() {
                        restoreAllControls();

                        for (var i = 0; i < steps.length; i++) {
                            var invalid = fieldControls(steps[i]).find(function (control) {
                                return !control.checkValidity();
                            });

                            if (invalid) {
                                activate(i);
                                invalid.reportValidity();

                                return false;
                            }
                        }

                        restoreAllControls();

                        return true;
                    }

                    function setLoading() {
                        if (!nextButton) return;

                        nextButton.disabled = true;
                        nextButton.dataset.loading = '1';
                        nextButton.innerHTML = submitLabel + ' <span class="spinner-border spinner-border-sm ms-2 align-middle" role="status" aria-hidden="true"></span>';
                    }

                    if (previousButton) {
                        previousButton.addEventListener('click', function () {
                            activate(current - 1);
                        });
                    }

                    if (nextButton) {
                        nextButton.addEventListener('click', function () {
                            if (current < steps.length - 1) {
                                if (validateStep(current)) activate(current + 1);
                                return;
                            }

                            if (!form || !validateAllSteps()) return;

                            setLoading();
                            form.submit();
                        });
                    }

                    if (form) {
                        form.addEventListener('submit', function (event) {
                            if (current < steps.length - 1) {
                                event.preventDefault();
                                if (validateStep(current)) activate(current + 1);
                                return;
                            }

                            if (!validateAllSteps()) {
                                event.preventDefault();
                            }
                        });
                    }

                    activate(current);
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('[data-form-wizard]').forEach(initWizard);
                });
            })();
        </script>
    @endpush
@endonce
