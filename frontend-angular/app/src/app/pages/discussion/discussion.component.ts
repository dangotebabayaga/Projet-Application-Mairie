import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { SocialService, ReseauSocial } from '../../services/social.service';

interface SocialDisplay {
  id: number;
  platform: string;
  platformKey: string;
  url: string;
  colorClass: string;
}

@Component({
  selector: 'app-discussion',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './discussion.component.html',
  styleUrls: ['./discussion.component.scss']
})
export class DiscussionComponent implements OnInit {
  socials: SocialDisplay[] = [];
  facebookEmbedUrl: SafeResourceUrl | null = null;
  loading = true;

  constructor(private socialService: SocialService, private sanitizer: DomSanitizer) {}

  ngOnInit(): void {
    this.loadSocials();
  }

  loadSocials(): void {
    this.loading = true;
    this.socialService.getAll().subscribe({
      next: (data: ReseauSocial[]) => {
        this.socials = data.map(r => this.toDisplay(r));
        this.facebookEmbedUrl = this.buildFacebookEmbed(data);
        this.loading = false;
      },
      error: (err: any) => {
        console.error('Erreur chargement réseaux sociaux', err);
        this.loading = false;
      }
    });
  }

  private toDisplay(r: ReseauSocial): SocialDisplay {
    const key = (r.plateform || '').toLowerCase();
    return {
      id: r.id,
      platform: r.plateform,
      platformKey: key,
      url: r.lien,
      colorClass: 'social-' + key
    };
  }

  private buildFacebookEmbed(reseaux: ReseauSocial[]): SafeResourceUrl | null {
    const fb = reseaux.find(r => (r.plateform || '').toLowerCase() === 'facebook');
    if (!fb || !fb.lien) return null;
    const params = new URLSearchParams({
      href: fb.lien,
      tabs: 'timeline',
      width: '500',
      height: '700',
      small_header: 'false',
      adapt_container_width: 'true',
      hide_cover: 'false',
      show_facepile: 'true'
    });
    const url = `https://www.facebook.com/plugins/page.php?${params.toString()}`;
    return this.sanitizer.bypassSecurityTrustResourceUrl(url);
  }

  openLink(url: string): void {
    window.open(url, '_blank', 'noopener,noreferrer');
  }
}
