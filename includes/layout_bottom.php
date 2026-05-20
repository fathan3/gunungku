    </section>
    <footer class="flex flex-col items-center gap-4 px-8 w-full py-8 mt-auto border-t border-[#C2C9BB]/20 bg-[#F9FAF2]">
      <div class="flex flex-wrap justify-center gap-6"><a class="text-xs uppercase tracking-widest text-[#42493E]/60 hover:text-[#2D5A27]" href="#">Safety Guidelines</a><a class="text-xs uppercase tracking-widest text-[#42493E]/60 hover:text-[#2D5A27]" href="#">Privacy Policy</a><a class="text-xs uppercase tracking-widest text-[#42493E]/60 hover:text-[#2D5A27]" href="#">Contact Support</a></div><p class="text-xs uppercase tracking-widest text-[#42493E]/60">© 2024 Gunungku Editorial. All rights reserved.</p>
    </footer>
  </main>
  <div class="md:hidden fixed bottom-0 left-0 right-0 glass-effect border-t border-outline-variant/20 z-50">
    <div class="flex overflow-x-auto px-2 py-2 gap-1 scrollbar-none">
    <?php $bottom=['dashboard'=>['dashboard','Home'],'discovery'=>['explore','Explore'],'simaksi'=>['assignment','Simaksi'],'checklist'=>['checklist','Checklist'],'komunitas'=>['groups','Community'],'profil'=>['person','Profil']]; foreach($bottom as $p=>$meta): ?>
      <a class="flex flex-col items-center gap-0.5 min-w-[56px] px-2 py-1.5 rounded-xl <?= currentActive($currentPage??'', $p)?'bg-primary/10 text-primary':'text-on-surface-variant' ?>" href="<?= e(pageUrl($p)) ?>"><span class="material-symbols-outlined text-[22px] <?= currentActive($currentPage??'', $p)?'material-fill':'' ?>"><?= e($meta[0]) ?></span><span class="text-[9px] font-bold uppercase tracking-wide"><?= e($meta[1]) ?></span></a>
    <?php endforeach; ?>
    </div>
  </div>
</div>
