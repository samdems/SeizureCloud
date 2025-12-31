<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- File Upload -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">File <span class="text-error">*</span></span>
            </label>
            <input
                type="file"
                wire:model="file"
                class="file-input file-input-bordered w-full @error('file') file-input-error @enderror"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp"
            />
            <label class="label">
                <span class="label-text-alt text-base-content/60">
                    Max 10MB. Accepted: PDF, Word, Images (JPG, PNG, GIF, WebP)
                </span>
            </label>
            @error('file')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror

            <div wire:loading wire:target="file" class="mt-2">
                <div class="flex items-center gap-2 text-sm text-base-content/60">
                    <span class="loading loading-spinner loading-sm"></span>
                    <span>Uploading file...</span>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">Title <span class="text-error">*</span></span>
            </label>
            <input
                type="text"
                wire:model="title"
                placeholder="Enter document title"
                class="input input-bordered w-full @error('title') input-error @enderror"
                maxlength="255"
            />
            @error('title')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">Description</span>
            </label>
            <textarea
                wire:model="description"
                placeholder="Add notes or details about this document (optional)"
                class="textarea textarea-bordered h-24 @error('description') textarea-error @enderror"
                maxlength="1000"
            ></textarea>
            @error('description')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Category -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">Category <span class="text-error">*</span></span>
            </label>
            <select
                wire:model="category"
                class="select select-bordered w-full @error('category') select-error @enderror"
            >
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('category')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Document Date -->
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text font-semibold">Document Date</span>
            </label>
            <input
                type="date"
                wire:model="document_date"
                class="input input-bordered w-full @error('document_date') input-error @enderror"
            />
            <label class="label">
                <span class="label-text-alt text-base-content/60">
                    Date shown on the document (if applicable)
                </span>
            </label>
            @error('document_date')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-2 pt-4">
            <button
                type="submit"
                class="btn btn-primary"
                wire:loading.attr="disabled"
                wire:target="file,save"
            >
                <span wire:loading.remove wire:target="save">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Document
                </span>
                <span wire:loading wire:target="save">
                    <span class="loading loading-spinner loading-sm"></span>
                    Uploading...
                </span>
            </button>
        </div>
    </form>
</div>
