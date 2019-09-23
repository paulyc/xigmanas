--- src/VBox/Runtime/r0drv/freebsd/memobj-r0drv-freebsd.c.orig	2018-10-15 14:31:31 UTC
+++ src/VBox/Runtime/r0drv/freebsd/memobj-r0drv-freebsd.c
@@ -105,6 +105,7 @@ static vm_map_t rtR0MemObjFreeBSDGetMap(PRTR0MEMOBJINT
 
 DECLHIDDEN(int) rtR0MemObjNativeFree(RTR0MEMOBJ pMem)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)pMem;
     int rc;
 
@@ -121,16 +122,15 @@ DECLHIDDEN(int) rtR0MemObjNativeFree(RTR0MEMOBJ pMem)
 
         case RTR0MEMOBJTYPE_LOCK:
         {
-            vm_map_t pMap = kernel_map;
+            if (pMemFreeBSD->Core.u.Lock.R0Process != NIL_RTR0PROCESS) {
+                vm_map_t pMap = &((struct proc *)pMemFreeBSD->Core.u.Lock.R0Process)->p_vmspace->vm_map;
 
-            if (pMemFreeBSD->Core.u.Lock.R0Process != NIL_RTR0PROCESS)
-                pMap = &((struct proc *)pMemFreeBSD->Core.u.Lock.R0Process)->p_vmspace->vm_map;
-
-            rc = vm_map_unwire(pMap,
+                rc = vm_map_unwire(pMap,
                                (vm_offset_t)pMemFreeBSD->Core.pv,
                                (vm_offset_t)pMemFreeBSD->Core.pv + pMemFreeBSD->Core.cb,
                                VM_MAP_WIRE_SYSTEM | VM_MAP_WIRE_NOHOLES);
-            AssertMsg(rc == KERN_SUCCESS, ("%#x", rc));
+                AssertMsg(rc == KERN_SUCCESS, ("%#x", rc));
+            }
             break;
         }
 
@@ -194,6 +194,7 @@ DECLHIDDEN(int) rtR0MemObjNativeFree(RTR0MEMOBJ pMem)
             return VERR_INTERNAL_ERROR;
     }
 
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return VINF_SUCCESS;
 }
 
@@ -224,18 +225,23 @@ static vm_page_t rtR0MemObjFreeBSDContigPhysAllocHelpe
 #else
         VM_OBJECT_UNLOCK(pObject);
 #endif
-        if (pPages)
+        if (pPages || cTries >= 1)
             break;
+#if __FreeBSD_version >= 1100092
+        if (!vm_page_reclaim_contig(fFlags, cPages, 0, VmPhysAddrHigh, uAlignment, 0))
+             break;
+#elif __FreeBSD_version >= 1000015
         vm_pageout_grow_cache(cTries, 0, VmPhysAddrHigh);
+#else
+        vm_contig_grow_cache(cTries, 0, VmPhysAddrHigh);
+#endif
         cTries++;
     }
-
-    return pPages;
 #else
-    while (cTries <= 1)
+    while (1)
     {
         pPages = vm_phys_alloc_contig(cPages, 0, VmPhysAddrHigh, uAlignment, 0);
-        if (pPages)
+        if (pPages || cTries >= 1)
             break;
         vm_contig_grow_cache(cTries, 0, VmPhysAddrHigh);
         cTries++;
@@ -243,11 +249,8 @@ static vm_page_t rtR0MemObjFreeBSDContigPhysAllocHelpe
 
     if (!pPages)
         return pPages;
-#if __FreeBSD_version >= 1000030
-    VM_OBJECT_WLOCK(pObject);
-#else
+
     VM_OBJECT_LOCK(pObject);
-#endif
     for (vm_pindex_t iPage = 0; iPage < cPages; iPage++)
     {
         vm_page_t pPage = pPages + iPage;
@@ -259,13 +262,9 @@ static vm_page_t rtR0MemObjFreeBSDContigPhysAllocHelpe
             atomic_add_int(&cnt.v_wire_count, 1);
         }
     }
-#if __FreeBSD_version >= 1000030
-    VM_OBJECT_WUNLOCK(pObject);
-#else
     VM_OBJECT_UNLOCK(pObject);
 #endif
     return pPages;
-#endif
 }
 
 static int rtR0MemObjFreeBSDPhysAllocHelper(vm_object_t pObject, u_long cPages,
@@ -292,16 +291,17 @@ static int rtR0MemObjFreeBSDPhysAllocHelper(vm_object_
 #else
             VM_OBJECT_LOCK(pObject);
 #endif
+
             while (iPage-- > 0)
             {
                 pPage = vm_page_lookup(pObject, iPage);
-#if __FreeBSD_version < 1000000
+#if __FreeBSD_version < 900000
                 vm_page_lock_queues();
 #endif
                 if (fWire)
                     vm_page_unwire(pPage, 0);
                 vm_page_free(pPage);
-#if __FreeBSD_version < 1000000
+#if __FreeBSD_version < 900000
                 vm_page_unlock_queues();
 #endif
             }
@@ -364,58 +364,77 @@ static int rtR0MemObjFreeBSDAllocHelper(PRTR0MEMOBJFRE
 }
 DECLHIDDEN(int) rtR0MemObjNativeAllocPage(PPRTR0MEMOBJINTERNAL ppMem, size_t cb, bool fExecutable)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)rtR0MemObjNew(sizeof(*pMemFreeBSD),
                                                                        RTR0MEMOBJTYPE_PAGE, NULL, cb);
     if (!pMemFreeBSD)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NO_MEMORY;
+    }
 
     int rc = rtR0MemObjFreeBSDAllocHelper(pMemFreeBSD, fExecutable, ~(vm_paddr_t)0, false, VERR_NO_MEMORY);
     if (RT_FAILURE(rc))
     {
         rtR0MemObjDelete(&pMemFreeBSD->Core);
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return rc;
     }
 
     *ppMem = &pMemFreeBSD->Core;
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return rc;
 }
 
 
 DECLHIDDEN(int) rtR0MemObjNativeAllocLow(PPRTR0MEMOBJINTERNAL ppMem, size_t cb, bool fExecutable)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)rtR0MemObjNew(sizeof(*pMemFreeBSD),
                                                                        RTR0MEMOBJTYPE_LOW, NULL, cb);
     if (!pMemFreeBSD)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NO_MEMORY;
+    }
 
     int rc = rtR0MemObjFreeBSDAllocHelper(pMemFreeBSD, fExecutable, _4G - 1, false, VERR_NO_LOW_MEMORY);
     if (RT_FAILURE(rc))
     {
         rtR0MemObjDelete(&pMemFreeBSD->Core);
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return rc;
     }
 
     *ppMem = &pMemFreeBSD->Core;
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return rc;
 }
 
 
 DECLHIDDEN(int) rtR0MemObjNativeAllocCont(PPRTR0MEMOBJINTERNAL ppMem, size_t cb, bool fExecutable)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
+
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)rtR0MemObjNew(sizeof(*pMemFreeBSD),
                                                                        RTR0MEMOBJTYPE_CONT, NULL, cb);
     if (!pMemFreeBSD)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NO_MEMORY;
+    }
 
     int rc = rtR0MemObjFreeBSDAllocHelper(pMemFreeBSD, fExecutable, _4G - 1, true, VERR_NO_CONT_MEMORY);
     if (RT_FAILURE(rc))
     {
         rtR0MemObjDelete(&pMemFreeBSD->Core);
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return rc;
     }
 
     pMemFreeBSD->Core.u.Cont.Phys = vtophys(pMemFreeBSD->Core.pv);
     *ppMem = &pMemFreeBSD->Core;
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return rc;
 }
 
@@ -425,6 +444,7 @@ static int rtR0MemObjFreeBSDAllocPhysPages(PPRTR0MEMOB
                                            RTHCPHYS PhysHighest, size_t uAlignment,
                                            bool fContiguous, int rcNoMem)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     uint32_t   cPages = atop(cb);
     vm_paddr_t VmPhysAddrHigh;
 
@@ -432,7 +452,10 @@ static int rtR0MemObjFreeBSDAllocPhysPages(PPRTR0MEMOB
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)rtR0MemObjNew(sizeof(*pMemFreeBSD),
                                                                        enmType, NULL, cb);
     if (!pMemFreeBSD)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NO_MEMORY;
+    }
 
     pMemFreeBSD->pObject = vm_object_allocate(OBJT_PHYS, atop(cb));
 
@@ -470,6 +493,7 @@ static int rtR0MemObjFreeBSDAllocPhysPages(PPRTR0MEMOB
         rtR0MemObjDelete(&pMemFreeBSD->Core);
     }
 
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return rc;
 }
 
@@ -489,17 +513,22 @@ DECLHIDDEN(int) rtR0MemObjNativeAllocPhysNC(PPRTR0MEMO
 DECLHIDDEN(int) rtR0MemObjNativeEnterPhys(PPRTR0MEMOBJINTERNAL ppMem, RTHCPHYS Phys, size_t cb, uint32_t uCachePolicy)
 {
     AssertReturn(uCachePolicy == RTMEM_CACHE_POLICY_DONT_CARE, VERR_NOT_SUPPORTED);
+    IPRT_FREEBSD_SAVE_EFL_AC();
 
     /* create the object. */
     PRTR0MEMOBJFREEBSD pMemFreeBSD = (PRTR0MEMOBJFREEBSD)rtR0MemObjNew(sizeof(*pMemFreeBSD), RTR0MEMOBJTYPE_PHYS, NULL, cb);
     if (!pMemFreeBSD)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NO_MEMORY;
+    }
 
     /* there is no allocation here, it needs to be mapped somewhere first. */
     pMemFreeBSD->Core.u.Phys.fAllocated = false;
     pMemFreeBSD->Core.u.Phys.PhysBase = Phys;
     pMemFreeBSD->Core.u.Phys.uCachePolicy = uCachePolicy;
     *ppMem = &pMemFreeBSD->Core;
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return VINF_SUCCESS;
 }
 
@@ -511,6 +540,7 @@ static int rtR0MemObjNativeLockInMap(PPRTR0MEMOBJINTER
                                      vm_offset_t AddrStart, size_t cb, uint32_t fAccess,
                                      RTR0PROCESS R0Process, int fFlags)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     int rc;
     NOREF(fAccess);
 
@@ -519,21 +549,28 @@ static int rtR0MemObjNativeLockInMap(PPRTR0MEMOBJINTER
     if (!pMemFreeBSD)
         return VERR_NO_MEMORY;
 
-    /*
-     * We could've used vslock here, but we don't wish to be subject to
-     * resource usage restrictions, so we'll call vm_map_wire directly.
-     */
-    rc = vm_map_wire(pVmMap,                                         /* the map */
-                     AddrStart,                                      /* start */
-                     AddrStart + cb,                                 /* end */
-                     fFlags);                                        /* flags */
+    if (pVmMap != kernel_map) {
+        /*
+         * We could've used vslock here, but we don't wish to be subject to
+         * resource usage restrictions, so we'll call vm_map_wire directly.
+         */
+        rc = vm_map_wire(pVmMap,                                         /* the map */
+                         AddrStart,                                      /* start */
+                         AddrStart + cb,                                 /* end */
+                         fFlags);                                        /* flags */
+    }
+    else
+        rc = KERN_SUCCESS;
+
     if (rc == KERN_SUCCESS)
     {
         pMemFreeBSD->Core.u.Lock.R0Process = R0Process;
         *ppMem = &pMemFreeBSD->Core;
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VINF_SUCCESS;
     }
     rtR0MemObjDelete(&pMemFreeBSD->Core);
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return VERR_NO_MEMORY;/** @todo fix mach -> vbox error conversion for freebsd. */
 }
 
@@ -569,6 +606,7 @@ DECLHIDDEN(int) rtR0MemObjNativeLockKernel(PPRTR0MEMOB
  */
 static int rtR0MemObjNativeReserveInMap(PPRTR0MEMOBJINTERNAL ppMem, void *pvFixed, size_t cb, size_t uAlignment, RTR0PROCESS R0Process, vm_map_t pMap)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
     int rc;
 
     /*
@@ -626,11 +664,13 @@ static int rtR0MemObjNativeReserveInMap(PPRTR0MEMOBJIN
         pMemFreeBSD->Core.pv = (void *)MapAddress;
         pMemFreeBSD->Core.u.ResVirt.R0Process = R0Process;
         *ppMem = &pMemFreeBSD->Core;
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VINF_SUCCESS;
     }
 
     rc = VERR_NO_MEMORY; /** @todo fix translation (borrow from darwin) */
     rtR0MemObjDelete(&pMemFreeBSD->Core);
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return rc;
 
 }
@@ -652,6 +692,8 @@ DECLHIDDEN(int) rtR0MemObjNativeReserveUser(PPRTR0MEMO
 DECLHIDDEN(int) rtR0MemObjNativeMapKernel(PPRTR0MEMOBJINTERNAL ppMem, RTR0MEMOBJ pMemToMap, void *pvFixed, size_t uAlignment,
                                           unsigned fProt, size_t offSub, size_t cbSub)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
+
 //  AssertMsgReturn(!offSub && !cbSub, ("%#x %#x\n", offSub, cbSub), VERR_NOT_SUPPORTED);
     AssertMsgReturn(pvFixed == (void *)-1, ("%p\n", pvFixed), VERR_NOT_SUPPORTED);
 
@@ -707,6 +749,7 @@ DECLHIDDEN(int) rtR0MemObjNativeMapKernel(PPRTR0MEMOBJ
             Assert((vm_offset_t)pMemFreeBSD->Core.pv == Addr);
             pMemFreeBSD->Core.u.Mapping.R0Process = NIL_RTR0PROCESS;
             *ppMem = &pMemFreeBSD->Core;
+            IPRT_FREEBSD_RESTORE_EFL_AC();
             return VINF_SUCCESS;
         }
         rc = vm_map_remove(kernel_map, Addr, Addr + cbSub);
@@ -715,6 +758,7 @@ DECLHIDDEN(int) rtR0MemObjNativeMapKernel(PPRTR0MEMOBJ
     else
         vm_object_deallocate(pMemToMapFreeBSD->pObject);
 
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return VERR_NO_MEMORY;
 }
 
@@ -722,6 +766,8 @@ DECLHIDDEN(int) rtR0MemObjNativeMapKernel(PPRTR0MEMOBJ
 DECLHIDDEN(int) rtR0MemObjNativeMapUser(PPRTR0MEMOBJINTERNAL ppMem, RTR0MEMOBJ pMemToMap, RTR3PTR R3PtrFixed, size_t uAlignment,
                                         unsigned fProt, RTR0PROCESS R0Process)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
+
     /*
      * Check for unsupported stuff.
      */
@@ -751,7 +797,12 @@ DECLHIDDEN(int) rtR0MemObjNativeMapUser(PPRTR0MEMOBJIN
     {
         /** @todo is this needed?. */
         PROC_LOCK(pProc);
-        AddrR3 = round_page((vm_offset_t)pProc->p_vmspace->vm_daddr + lim_max(pProc, RLIMIT_DATA));
+        AddrR3 = round_page((vm_offset_t)pProc->p_vmspace->vm_daddr +
+#if __FreeBSD_version >= 1100077
+                            lim_max_proc(pProc, RLIMIT_DATA));
+#else
+                            lim_max(pProc, RLIMIT_DATA));
+#endif
         PROC_UNLOCK(pProc);
     }
     else
@@ -793,6 +844,7 @@ DECLHIDDEN(int) rtR0MemObjNativeMapUser(PPRTR0MEMOBJIN
             Assert((vm_offset_t)pMemFreeBSD->Core.pv == AddrR3);
             pMemFreeBSD->Core.u.Mapping.R0Process = R0Process;
             *ppMem = &pMemFreeBSD->Core;
+            IPRT_FREEBSD_RESTORE_EFL_AC();
             return VINF_SUCCESS;
         }
 
@@ -802,19 +854,25 @@ DECLHIDDEN(int) rtR0MemObjNativeMapUser(PPRTR0MEMOBJIN
     else
         vm_object_deallocate(pMemToMapFreeBSD->pObject);
 
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     return VERR_NO_MEMORY;
 }
 
 
 DECLHIDDEN(int) rtR0MemObjNativeProtect(PRTR0MEMOBJINTERNAL pMem, size_t offSub, size_t cbSub, uint32_t fProt)
 {
+    IPRT_FREEBSD_SAVE_EFL_AC();
+
     vm_prot_t          ProtectionFlags = 0;
     vm_offset_t        AddrStart       = (uintptr_t)pMem->pv + offSub;
     vm_offset_t        AddrEnd         = AddrStart + cbSub;
     vm_map_t           pVmMap          = rtR0MemObjFreeBSDGetMap(pMem);
 
     if (!pVmMap)
+    {
+        IPRT_FREEBSD_RESTORE_EFL_AC();
         return VERR_NOT_SUPPORTED;
+    }
 
     if ((fProt & RTMEM_PROT_NONE) == RTMEM_PROT_NONE)
         ProtectionFlags = VM_PROT_NONE;
@@ -826,6 +884,7 @@ DECLHIDDEN(int) rtR0MemObjNativeProtect(PRTR0MEMOBJINT
         ProtectionFlags |= VM_PROT_EXECUTE;
 
     int krc = vm_map_protect(pVmMap, AddrStart, AddrEnd, ProtectionFlags, FALSE);
+    IPRT_FREEBSD_RESTORE_EFL_AC();
     if (krc == KERN_SUCCESS)
         return VINF_SUCCESS;
 
@@ -850,11 +909,19 @@ DECLHIDDEN(RTHCPHYS) rtR0MemObjNativeGetPagePhysAddr(P
 
             vm_offset_t pb = (vm_offset_t)pMemFreeBSD->Core.pv + ptoa(iPage);
 
-            struct proc    *pProc     = (struct proc *)pMemFreeBSD->Core.u.Lock.R0Process;
-            struct vm_map  *pProcMap  = &pProc->p_vmspace->vm_map;
-            pmap_t pPhysicalMap       = vm_map_pmap(pProcMap);
+            if (pMemFreeBSD->Core.u.Mapping.R0Process != NIL_RTR0PROCESS)
+            {
+                RTHCPHYS addr;
+                IPRT_FREEBSD_SAVE_EFL_AC();
+                struct proc    *pProc     = (struct proc *)pMemFreeBSD->Core.u.Lock.R0Process;
+                struct vm_map  *pProcMap  = &pProc->p_vmspace->vm_map;
+                pmap_t pPhysicalMap       = vm_map_pmap(pProcMap);
 
-            return pmap_extract(pPhysicalMap, pb);
+                addr =  pmap_extract(pPhysicalMap, pb);
+                IPRT_FREEBSD_RESTORE_EFL_AC();
+                return addr;
+            }
+            return vtophys(pb);
         }
 
         case RTR0MEMOBJTYPE_MAPPING:
@@ -863,11 +930,15 @@ DECLHIDDEN(RTHCPHYS) rtR0MemObjNativeGetPagePhysAddr(P
 
             if (pMemFreeBSD->Core.u.Mapping.R0Process != NIL_RTR0PROCESS)
             {
+                RTHCPHYS addr;
+                IPRT_FREEBSD_SAVE_EFL_AC();
                 struct proc    *pProc     = (struct proc *)pMemFreeBSD->Core.u.Mapping.R0Process;
                 struct vm_map  *pProcMap  = &pProc->p_vmspace->vm_map;
                 pmap_t pPhysicalMap       = vm_map_pmap(pProcMap);
 
-                return pmap_extract(pPhysicalMap, pb);
+                addr =  pmap_extract(pPhysicalMap, pb);
+                IPRT_FREEBSD_RESTORE_EFL_AC();
+                return addr;
             }
             return vtophys(pb);
         }
@@ -877,6 +948,7 @@ DECLHIDDEN(RTHCPHYS) rtR0MemObjNativeGetPagePhysAddr(P
         case RTR0MEMOBJTYPE_PHYS_NC:
         {
             RTHCPHYS addr;
+            IPRT_FREEBSD_SAVE_EFL_AC();
 #if __FreeBSD_version >= 1000030
             VM_OBJECT_WLOCK(pMemFreeBSD->pObject);
 #else
@@ -888,6 +960,7 @@ DECLHIDDEN(RTHCPHYS) rtR0MemObjNativeGetPagePhysAddr(P
 #else
             VM_OBJECT_UNLOCK(pMemFreeBSD->pObject);
 #endif
+            IPRT_FREEBSD_RESTORE_EFL_AC();
             return addr;
         }
 
