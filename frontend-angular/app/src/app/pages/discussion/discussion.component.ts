import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

type SocialPlatform = 'facebook' | 'twitter' | 'instagram';

interface SocialPost {
  id: string;
  platform: SocialPlatform;
  content: string;
  imageUrl?: string;
  postedAt: Date;
  link: string;
}

interface SocialLink {
  name: string;
  platform: SocialPlatform;
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
export class DiscussionComponent {
  socialLinks: SocialLink[] = [
    { name: 'Facebook', platform: 'facebook', url: 'https://facebook.com', colorClass: 'social-facebook' },
    { name: 'Twitter', platform: 'twitter', url: 'https://twitter.com', colorClass: 'social-twitter' },
    { name: 'Instagram', platform: 'instagram', url: 'https://instagram.com', colorClass: 'social-instagram' }
  ];

  // Données de démonstration
  posts: SocialPost[] = [
    {
      id: '1',
      platform: 'facebook',
      content: '🎄 Les illuminations de Noël sont installées !\n\nVenez les découvrir dès ce soir dans tout le centre-ville. Un parcours féerique vous attend avec plus de 50 000 ampoules LED.\n\n📍 Départ place de la Mairie\n🕕 De 17h à minuit',
      imageUrl: 'https://images.unsplash.com/photo-1512389142860-9c449e58a814?w=600',
      postedAt: new Date('2024-01-15T18:30:00'),
      link: 'https://facebook.com/post/1'
    },
    {
      id: '2',
      platform: 'twitter',
      content: '📢 Rappel : La collecte des sapins de Noël commence demain !\n\nDéposez votre sapin (sans décoration) aux points de collecte habituels. Ils seront transformés en compost. 🌱\n\n#VilleVerte #Recyclage',
      postedAt: new Date('2024-01-14T10:15:00'),
      link: 'https://twitter.com/post/2'
    },
    {
      id: '3',
      platform: 'instagram',
      content: '📸 Retour en images sur le marché de Noël 2023 !\n\nMerci à tous les visiteurs et exposants pour cette belle édition. Rendez-vous l\'année prochaine ! ✨',
      imageUrl: 'https://images.unsplash.com/photo-1545622783-b3e021430fee?w=600',
      postedAt: new Date('2024-01-12T14:00:00'),
      link: 'https://instagram.com/post/3'
    },
    {
      id: '4',
      platform: 'facebook',
      content: '🏛️ Conseil municipal ce soir à 18h30\n\nOrdre du jour :\n- Budget 2024\n- Projet de réaménagement du centre-ville\n- Questions diverses\n\nOuvert au public ! Salle du Conseil, Mairie.',
      postedAt: new Date('2024-01-10T09:00:00'),
      link: 'https://facebook.com/post/4'
    },
    {
      id: '5',
      platform: 'twitter',
      content: '⚠️ Travaux rue Victor Hugo\n\nCirculation alternée du 15 au 20 janvier. Merci de votre compréhension.\n\nPlus d\'infos : maville.fr/travaux',
      postedAt: new Date('2024-01-08T11:30:00'),
      link: 'https://twitter.com/post/5'
    }
  ];

  getPlatformClass(platform: SocialPlatform): string {
    return `platform-${platform}`;
  }

  formatDate(date: Date): string {
    return new Date(date).toLocaleDateString('fr-FR', {
      day: 'numeric',
      month: 'long',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  openLink(url: string): void {
    window.open(url, '_blank', 'noopener,noreferrer');
  }

  onLike(postId: string): void {
    console.log('Like post:', postId);
  }

  onComment(postId: string): void {
    console.log('Comment post:', postId);
  }

  onShare(postId: string): void {
    console.log('Share post:', postId);
  }

  loadMore(): void {
    console.log('Load more posts');
  }
}
