<?php
  include("apc_functions.php");

  BeginPage();
  echo("<A TARGET=_apc HREF=http://apc.communityconnect.com/faq.html>View the most recent FAQ online at apc homepage</A><P>");
?>
        <FONT SIZE=3 COLOR="#CC3300" FACE="ARIAL,HELVETICA,VERDANA"><B>FREQUENTLY ASKED QUESTIONS</B></FONT><BR>
        <FONT SIZE=2 COLOR="#000000" FACE="VERDANA, ARIAL,HELVETICA">
<TT>&nbsp;1</TT>. <A HREF="#one">How does all this stuff work?</A><BR>
<TT>&nbsp;2</TT>. <A HREF="#two">Why are there two implementations? Which one is right for me?</A><BR>
<TT>&nbsp;3</TT>. <A HREF="#three">What sort of performance benefit will I receive?</A><BR>
<TT>&nbsp;4</TT>. <A HREF="#four">When I make changes to my files, the changes do not appear. Why not?</A><BR>
<TT>&nbsp;5</TT>. <A HREF="#five">I'm running the shm implementation and it leaks semaphores. How can I fix this?</A><BR>
<TT>&nbsp;6</TT>. <A HREF="#six">Why do I get "call to undefined function" PHP &#101;rrors with APC but not without it?</A><BR>
<TT>&nbsp;7</TT>. <A HREF="#seven">Why do I keep running out of open files when I use the mmap version?</A><BR>
<TT>&nbsp;8</TT>. <A HREF="#eight">What use are per object TTLs?</A><BR>
<TT>&nbsp;9</TT>. <A HREF="#nine">Why can't I specify multiple exclusion filters in <CODE>php.ini</CODE>?</A><BR>
<TT>10</TT>. <A HREF="#ten">How do I remove all the cache entries under the mmap version?</A><BR>
<TT>11</TT>. <A HREF="#eleven">Why don't my derived classes work correctly?</A><BR>
<TT>12</TT>. <A HREF="#twelve">I just upgraded my version of PHP, and now I get unresolved symbol &#101;rrors. What gives?</A><BR>
<TT>13</TT>. <A HREF="#thirteen">Why do weird things happen when I pass relative paths to <CODE>include</CODE>?</A><BR>
<TT>14</TT>. <A HREF="#fourteen">Why is the value of <CODE>include_path</CODE> in my <CODE>php.ini</CODE> being ignored?</A><BR>
<BR><BR>
<A NAME="one">
<B>1. How does all this stuff work?</B><BR><BR>
The APC cache works by storing the compiled instructions for a PHP script in
shared memory (either SystemV shared memory or memory-mapped files). When a
file is run, the extension checks to see if it is cached. If so, the
instructions are read from memory and the compilation step is bypassed.
Otherwise the file is compiled as usual and inserted into the cache.
</A>
<BR><BR>
<A NAME="two">
<B>2. Why are there two implementations? Which one is right for me?</B><BR><BR>
We wrote two implementations, one using SystemV semaphores and shared memory
and the other using memory-mapped files and file locking, for portability
between Unix systems and for testing. Both support all of the basic cache
operations, but they are not functionally identical. For example, the shared
memory implementation (shm) is capable of much finer control of cached objects
than the memory-mapped file implementation (mmap).
<BR><BR>
Here is a list of pros and cons for each:
<BR><BR>
<PRE>
   shm
        pros:
            - supports per object time-to-live values
            - cache statistics available for entire webserver
        cons:
            - as with all programs that use SystemV semaphores, it has the
              potential to "leak" semaphores
    
    mmap
        pros:
            - greater visibility into the inner workings of the cache
            - can be used as a general compiler, because the cached objects
              are stored as files
        cons:
            - may require many open files
            - cache statistics are per process, not per host
            - cache management is more difficult
</PRE>
Our internal tests have shown little difference in speed between the two
implementations under typical conditions. There are unusual situations,
however, under which one implementation will perform better than the other.
For example, in common situations, shm tends to perform slightly better;
but if a webserver serves a very small number of pages, mmap may perform
better because it reduces contention between the webserver processes. If
you need optimal performance, our advice is to try both implementations
and make a decision based on benchmark data.
</A>
<BR><BR>
<A NAME="three">
<B>3. What sort of performance benefit will I receive?</B><BR><BR>
Ahh... the magic question. This really depends on the size and complexity
of your PHP code. A 50% performance improvement is typical for a "Hello
World" program, but the potential benefit is greater as the code increases
in complexity. We saw a roughly 400% performance improvement in our production
environment. As always, your mileage may vary.
</A>
<BR><BR>
<A NAME="four">
<B>4. When I make changes to my files, the changes do not appear. Why not?</B><BR><BR>
APC does not know when you have updated a file, and it will continue to use
the older, cached version of the page, unless one of the following occurs:
you call the PHP function <CODE>apc_reset_cache</CODE>; you call the PHP
function <CODE>apc_rm</CODE> and provide the filename as an argument; the page
expires; or you restart the webserver. The default time-to-live for all cached
objects is set in the <CODE>php.ini</CODE> file; you can also set the TTL for
an individual file by calling <CODE>apc_set_my_ttl</CODE> in the file.<BR>
<BR>
Another option is to set <CODE>apc.check_mtime</CODE> to <CODE>1</CODE> in your
<CODE>php.ini</CODE> file; at a slight cost in efficiency, APC will
automatically update the cache whenever files are modified. Note that this
works only with the shm implementation.
</A>
<BR><BR>
<A NAME="five">
<B>5. I'm running the shm implementation and it leaks semaphores. How can I fix this?</B><BR><BR> The simple answer is you can't. If you are willing and able to make minor adjustments to your webserver startup or shutdown scripts, and if <CODE>ipcs</CODE> and <CODE>ipcrm</CODE> are installed on your system (they should be), you can practically eliminate the problem. You simply need to remove all remaining semaphores from your system when your webservers starts or stops (one or the other). (Beware that this technique, if applied naively, will break other applications running on the webserver that also use semaphores.)
</A>
<BR><BR>
<A NAME="six">
<B>6. Why do I get "call to undefined function" PHP &#101;rrors with APC but not without it?</B><BR><BR>
Your code creates a conflict in the global namespace, but PHP manages to deal
with it. APC, however, will fail to correctly cache the conflicting modules if
it finds a namespace conflict. Here is an example of how this can occur:
<BR><BR>
<PRE>
        File <CODE>A.php</CODE> includes file <CODE>B.php</CODE>, which
        defines the function <CODE>foobar</CODE> and sets some variable,
        say, <CODE>$B_INCLUDED</CODE>, to indicate that it has been included.

        File <CODE>A.php</CODE> now includes file <CODE>C.php</CODE>, which
        also defines a function named <CODE>foobar</CODE>. Before it gets to
        the function definition, though, <CODE>C.php</CODE> returns if
        <CODE>$B_INCLUDED</CODE> is true.
</PRE>
PHP permits this kind of conditional inclusion, but it will fail with APC
because <CODE>C.php</CODE> is always included and compiled, even though it
never reaches the statement that declares <CODE>foobar</CODE> for the second
time. We made the (possibly questionable) decision to designate this a
programming &#101;rror, so you must "correct" your code before you can use
APC.
</A>
<BR><BR>
<A NAME="seven">
<B>7. Why do I keep running out of open files when I use the mmap version?</B><BR><BR>
The mmap implementation of APC requires a lot of open files. One per source
file per webserver process, to be precise. On most Unix systems, it is easy
to raise the maximum number of open files per process. Under Linux, for
example, you can change the value in <CODE>/proc/sys/fs/file-max</CODE>.
</A>
<BR><BR>
<A NAME="eight">
<B>8. What use are per object TTLs?</B><BR><BR>
Specifying the time-to-live, i.e. the lifetime, of a file (by calling the PHP
function <CODE>apc_set_my_ttl</CODE>) allows you cache files that are changed
frequently. If, for example, you know that a page is updated every five
minutes but you would still like to cache it for those five minutes, you can
simply set that page's individual TTL to 300 seconds:
<BR><BR>
<PRE>
    &lt;?
        // Set TTL for this page to 5 minutes.
        apc_set_my_ttl(300);

        // Your code here...
    ?&gt;
</PRE>
Thus, you can even cache pages that change frequently. Note that this works
only with the shm implementation.
</A>
<BR><BR>
<A NAME="nine">
<B>9. Why can't I specify multiple exclusion filters in <CODE>php.ini</CODE>?</B>
<BR><BR>
Because PHP does not support multiple values for a single ini entry and we
were too lazy to provide a workaround. If there is a great need for this
feature from the public, we may implement it in the future. Of course, you
are encouraged to implement and contribute it yourself!
</A>
<BR><BR>
<A NAME="ten">
<B>10. How do I remove all the cache entries under the mmap version?</B><BR><BR>
<CODE>find /your/cache/dir -name '\*_apc' -exec rm {} \;</CODE>
<BR><BR>
For what should be obvious reasons we felt uncomfortable adding that
functionality to <CODE>apc_reset_cache</CODE>.
<BR><BR>
<A NAME="eleven">
<B>11. Why don't my derived classes work correctly?</B><BR><BR>
Support for class inheritance was introduced in version 1.0.4 of APC.
<BR><BR>
<A NAME="twelve">
<B>12. I just upgraded my version of PHP, and now I get unresolved symbol &#101;rrors. What gives?</B><BR><BR>
Between versions of PHP, the definitions of certain internal macros may
change. If you compile APC with one version of PHP and then run it under
another version, APC may fail to load because the PHP symbols in the prior
version no longer exist. The only way to fix this is to recompile APC under
the same version of PHP you are currently running.
<BR><BR>
<A NAME="thirteen">
<B>13. Why do weird things happen when I pass relative paths to <CODE>include</CODE>?</B><BR><BR>
Because you haven't enabled <CODE>apc.relative_includes</CODE> in your
<CODE>php.ini</CODE> file. Unless instructed <I>not</I> to do so, APC treats
identical relative include paths that point to different files as the same
file.<BR>
<BR>
Note: relative include paths may add substantial overhead to APC, because
it forces APC to perform multiple file system operations for each file.
Without relative include support, APC performs no file system operations
(shm implementation) or about three (mmap implementation) per file.
Using the PHP <CODE>include_path</CODE> imposes even more overhead. (Of
course, running with APC is always faster than running without any caching
at all.)
<BR><BR>
<A NAME="fourteen">
<B>14. Why is the value of <CODE>include_path</CODE> in my <CODE>php.ini</CODE> being ignored?</B><BR><BR>
Because you haven't enabled <CODE>apc.relative_includes</CODE> in your
<CODE>php.ini</CODE> file. See <A HREF="#thirteen">question 13</A> for details.
<BR><BR>
</A>
        </FONT>
        
        
<?php

  EndPage();
?>
