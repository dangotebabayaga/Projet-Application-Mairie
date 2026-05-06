import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-photo-upload',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './photo-upload.component.html',
  styleUrls: ['./photo-upload.component.scss']
})
export class PhotoUploadComponent {
  @Input() label = 'Photo';
  @Input() hint = 'Optionnel : JPG, PNG, WEBP — max 5 Mo';
  @Input() maxSizeMb = 5;
  @Input() inputId = 'photo-upload-' + Math.random().toString(36).slice(2, 8);

  @Output() fileSelected = new EventEmitter<File | null>();
  @Output() errorChange = new EventEmitter<string | null>();

  selectedFile: File | null = null;
  preview: string | null = null;
  error: string | null = null;

  onChange(event: Event): void {
    this.error = null;
    this.errorChange.emit(null);

    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] || null;

    if (!file) {
      this.clearInternal();
      this.fileSelected.emit(null);
      return;
    }

    if (!file.type.startsWith('image/')) {
      this.setError('Le fichier doit être une image', input);
      return;
    }
    if (file.size > this.maxSizeMb * 1024 * 1024) {
      this.setError(`Image trop volumineuse (max ${this.maxSizeMb} Mo)`, input);
      return;
    }

    this.selectedFile = file;
    const reader = new FileReader();
    reader.onload = e => this.preview = e.target?.result as string;
    reader.readAsDataURL(file);

    this.fileSelected.emit(file);
  }

  remove(): void {
    this.clearInternal();
    this.fileSelected.emit(null);
  }

  /** Méthode publique appelable depuis le parent (via @ViewChild) après un reset de form */
  reset(): void {
    this.clearInternal();
  }

  private clearInternal(): void {
    this.selectedFile = null;
    this.preview = null;
    this.error = null;
  }

  private setError(message: string, input: HTMLInputElement): void {
    this.error = message;
    this.errorChange.emit(message);
    this.selectedFile = null;
    this.preview = null;
    input.value = '';
    this.fileSelected.emit(null);
  }
}
