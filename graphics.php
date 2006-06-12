<?php
require_once("docutil.php");
page_head("The BOINC graphics API");
echo"
<p>
BOINC applications can optionally provide graphics,
which are displayed either in an application window
or in a full-screen window
(when the BOINC screensaver is selected).


<p>
You are encouraged to implement graphics using OpenGL.
This makes it easy for your application to show graphics on all platforms.



<h2>Integrated graphics</h2>
<p>
Graphics can either be integrated in your main application
or generated by a separate program.
The integrated approach is recommended, and we'll describe it first.
In this approach, instead of boinc_init(),
an application calls
".html_text("
#if defined(_WIN32) || defined(__APPLE__)
    retval = boinc_init_graphics(worker);
#else
    retval = boinc_init_graphics_lib(worker, argv[0]);
#endif
")."
where <code>worker()</code> is the main function of your application.
Your application must supply
rendering and input-handling functions (see below).
<p>
These functions creates a <b>worker thread</b>
that runs the main application function.
The original thread becomes the <b>graphics thread</b>,
which handles GUI events and does rendering.

<p>
On Unix, your graphics code must be put in a separate
shared library (.so) file.
This is because
Unix hosts may not have the needed libraries (OpenGL, GLUT, X11).
If an application is linked dynamically to these libraries,
it will fail on startup if the libraries are not present.
On the other hand,
if an application is linked statically to these libraries,
graphics will be done very inefficiently on most hosts.
<p>
The shared library must have the same name as the
executable followed by '.so'.
It must be linked with libboinc_graphics_impl.a,
with your rendering and input-handling functions,
and (dynamically) with glut and opengl.
You must bundle the main program and the shared library together as a
<a href=tool_update_versions.php>multi-file application version</a>.
Unix/Linux applications that use graphics should compile
all files with -D_REENTRANT,
since graphics uses multiple threads.

<p>
The <a href=example.php>BOINC example application</a>
uses this technique, and shows the Makefile command that are
needed to produce the shared library on Unix.

<h2>Rendering and input-handling functions</h2>
<p>
Programs that use integrated graphics must supply the following functions:
<pre>
    void app_graphics_render(int xs, ys, double time_of_day);
</pre>
This will be called periodically in the graphics thread.
It should generate the current graphic.
<code>xs</code> and <code>ys</code> are the X and Y sizes of the window,
and <code>time_of_day</code> is the relative time in seconds.
Applications that don't do graphics must also supply a
dummy <code>app_graphics_render()</code> to link with the API.
<pre>
    void app_graphics_init();
</pre>
This is called in the graphics thread when a window is created.
It must make any calls needed to initialize graphics in the window.
<pre>
    void app_graphics_resize(int x, int y);
</pre>
Called when the window size changes.

<pre>
    void app_graphics_reread_prefs();
</pre>
This is called, in the graphics thread, whenever
the user's project preferences change.
It can call
".html_text("
    boinc_parse_init_data_file();
    boinc_get_init_data(APP_INIT_DATA&);
")."
to get the new preferences.

<p>
The application must supply the following input-handling functions:
<pre>
void boinc_app_mouse_move(
    int x, int y,       // new coords of cursor
    int left,          // whether left mouse button is down
    int middle,
    int right
);

void boinc_app_mouse_button(
    int x, int y,       // coords of cursor
    int which,          // which button (0/1/2)
    int is_down        // true iff button is now down
);

void boinc_app_key_press(
    int, int            // system-specific key encodings
)

void boinc_app_key_release(
    int, int            // system-specific key encodings
)
</pre>
<h3>Limiting frame rate</h3>
<p>
The following global variables control frame rate:
<p>
<b>boinc_max_fps</b> is an upper bound on the number of frames per second
(default 30).
<p>
<b>boinc_max_gfx_cpu_frac</b> is an upper bound on the fraction
of CPU time used for graphics (default 0.5).

<h3>Support classes</h3>
<p>
Several graphics-related classes were developed for SETI@home.
They may be of general utility.

<dl>
<dt>
REDUCED_ARRAY
<dd>
Represents a two-dimensional array of data,
which is reduced to a smaller dimension by averaging or taking extrema.
Includes member functions for drawing the reduced data as a 3D graph
in several ways (lines, rectangles, connected surface).
<dt>
PROGRESS and PROGRESS_2D
<dd>
Represent progress bars, depicted in 3 or 2 dimensions.

<dt>
RIBBON_GRAPH
<dd>
Represents of 3D graph of a function of 1 variable.

<dt>
MOVING_TEXT_PANEL
<dd>
Represents a flanged 3D panel, moving cyclically in 3 dimentions,
on which text is displayed.
<dt>
STARFIELD
<dd>
Represents a set of randomly-generated stars
that move forwards or backwards in 3 dimensions.

<dt>
TEXTURE_DESC
<dd>
Represents an image (JPEG, Targa, BMP, PNG, or RGB)
displayed in 3 dimensions.
</dl>
<p>
The file api/txf_util.C has support functions from
drawing nice-looking 3D text.


<h3>Static graphics</h3>
<p>
An application can display a pre-existing image file
(JPEG, GIFF, BMP or Targa) as its graphic.
This is the simplest approach since you
don't need to develop any code.
You must include the image file with each workunit.
To do this, link the application with api/static_graphics.C
(edit this file to use your filename).
You can change the image over time,
but you must change the (physical, not logical)
name of the file each time.

<h2>Graphics in a separate program</h3>
<p>
In this approach, an application bundles a
'main program' and a 'graphics program'.
The main program executes the graphics program, and kills it when done.
The main and graphics programs typically communicate using shared memory;
you can use the functions in boinc/lib/shmem.C for this.
<p>
The main program should initialize using
".html_text("
    int boinc_init_options_graphics(BOINC_OPTIONS&, WORKER_FUNC_PTR worker);
")."
<p>
The graphics application can be implemented
using the BOINC framework, in which case it must initialize with
".html_text("
    int boinc_init_options_graphics(BOINC_OPTIONS&, NULL);
")."
and supply rendering and input-handling functions.

<p>
Either the graphics or the main program can handle
graphics messages from the core client.
It's easiest to have the graphics program handle them;
if the main program handles them, it must convey them to the graphics program.
";
page_tail();
?>
